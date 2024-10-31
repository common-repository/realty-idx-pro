<?php
/**
 * Author: Paul Grejaldo
 * Date: 2016/10/09
 * Time: 11:38 PM
 */

namespace IDXRealtyPro\Helper;

if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}

class Cli
{

    /**
     * Get default command
     *
     * @return string
     */
    public static function getCommand()
    {
        return 'idxrp';
    }

    /**
     * Get wp-cli phar file path
     *
     * @return string The wp-cli phar file path
     */
    public static function getPharPath()
    {
        global $is_IIS;

        if ( defined( 'IDXRP_CLI_PHAR_PATH' ) && IDXRP_CLI_PHAR_PATH !== '' ) {
            $phar_file = sprintf( '%s/%s', untrailingslashit( IDXRP_CLI_PHAR_PATH ), 'wp-cli.phar' );
        } else if ( $is_IIS ) {
            $phar_file = sprintf( '%s\%s', dirname( $_SERVER['DOCUMENT_ROOT'] ), 'wp-cli.phar' );
        } else {
            $dir_path  = explode( '/', $_SERVER['DOCUMENT_ROOT'] );
            $phar_file = sprintf( '/%s/%s/%s', $dir_path[1], $dir_path[2], 'wp-cli.phar' );
        }

        return apply_filters( 'idxrp_get_wp_cli_phar_path', $phar_file );
    }

    /**
     * Checks if wp-cli exists
     *
     * @return bool True on success, false otherwise
     */
    public static function exists()
    {
        $result = false;

        $phar_file = self::getPharPath();

        if ( function_exists( 'shell_exec' ) && file_exists( $phar_file ) ) {
            $php_bin = self::getPhpBinary();

            if ( $php_bin ) {
                $output = shell_exec( "{$php_bin} {$phar_file} --info" );
                if ( strpos( $output, 'Content-type: text/html' ) === false &&
                     strpos( $output, 'Could not open input file' ) === false &&
                     strpos( $output, 'WP-CLI version' ) !== false
                ) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Get WP-CLI version status - needs update or not
     *
     * @return string 'update' if new version is found, 'equal' if current version is the same with source
     */
    public static function getVersionStatus()
    {
        $key = sha1( 'idxrp-wp-cli-update' );

        $wp_cli_update = get_transient( $key );

        if ( ! $wp_cli_update ) {
            $wp_cli_update = Cli::execute( 'cli check-update', false );
            if ( is_string( $wp_cli_update ) &&
                 ( false !== stripos( $wp_cli_update, 'Success: WP-CLI is at the latest version.' ) )
            ) {
                $wp_cli_update = 'equal';
            } else {
                $wp_cli_update = 'update';
            }
            set_transient( $key, $wp_cli_update, HOUR_IN_SECONDS * 12 );
        }

        return $wp_cli_update;
    }

    /**
     * Set WP-CLI version status
     *
     * @param string $value
     */
    public static function setVersionStatus( $value )
    {
        $key = sha1( 'idxrp-wp-cli-update' );
        set_transient( $key, $value, HOUR_IN_SECONDS * 12 );
    }

    /**
     * Get the path to the PHP binary used when executing WP-CLI.
     *
     * Environment values permit specific binaries to be indicated.
     *
     * @access   public
     * @category System
     *
     * @return string
     */
    public static function getPhpBinary()
    {
        $php_bin = 'php';
        if ( defined( 'CUSTOM_PHP_BINARY' ) && CUSTOM_PHP_BINARY ) {
            $php_bin = CUSTOM_PHP_BINARY;
        } else if ( defined( 'PHP_BINDIR' ) && PHP_BINDIR ) {
            $php_bin = trailingslashit( PHP_BINDIR ) . 'php';
        } else if ( defined( 'PHP_BINARY' ) && PHP_BINARY ) {
            $php_bin = PHP_BINARY;
        }

        return $php_bin;
    }

    /**
     * Execute a wp-cli subcommand
     *
     * @param string $subcommand   The subcommand to execute
     * @param bool   $with_command Whether to execute with plugin's main command or not
     *                             (e.g. "php wp-cli.phar idxrp <subcommand>" OR "php wp-cli.phar <subcommand>")
     * @param string $log_file     The log file path
     *
     * @return bool|string If log file is passed, then the PID of the process, otherwise the command output
     */
    public static function execute( $subcommand, $with_command = true, $log_file = '' )
    {
        $phar_file = static::getPharPath();

        $result = false;
        if ( self::exists() ) {
            $php_bin = self::getPhpBinary();

            if ( $php_bin ) {
                $command    = $with_command ? ' ' . self::getCommand() . ' ' : '';
                $subcommand = " {$subcommand}";

                $target_url = '';
                if ( is_multisite() ) {
                    $target_url = sprintf( ' --url=%s', home_url() );
                }

                $use_max_memory = ( ( defined( 'IDXRP_CLI_UNLI_MEMORY' ) && IDXRP_CLI_UNLI_MEMORY ) ||
                                    apply_filters( 'idxrp_cli_unli_memory', false ) );
                if ( $use_max_memory && wp_is_ini_value_changeable( 'memory_limit' ) ) {
                    $php_bin .= ' -d memory_limit=-1';
                }
                if ( empty( $log_file ) ) {
                    $handle = popen(
                        sprintf( "{$php_bin} {$phar_file}%s%s%s 2>&1", $command, $subcommand, $target_url ),
                        "r"
                    );
                    $result = fread( $handle, 2096 );
                    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                        while ( ! feof( $handle ) ) {
                            echo $result;
                            flush();
                        }
                    }
                    pclose( $handle );
                } else {
                    $process = proc_open(
                        sprintf(
                            "{$php_bin} {$phar_file}%s%s%s %s",
                            $command,
                            $subcommand,
                            $target_url,
                            "> {$log_file} 2>&1 & echo $!"
                        ),
                        [
                            [ "pipe", "r" ],
                            [ "pipe", "w" ],
                            [ "pipe", "w" ]
                        ],
                        $pipes
                    );
                    $result  = stream_get_contents( $pipes[1] );
                    $result  = str_replace( "\n", '', $result );
                    $result  = intval( $result );
                    fclose( $pipes[1] );
                    proc_close( $process );
                }
            }
        }

        return $result;
    }

    /**
     * Prints a json formatted string in command line
     *
     * @param string $message The message string to print
     */
    public static function printJson( $message )
    {
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            $message = strip_tags( $message );
            $message = str_replace( [ "\r\n", "\n\r", "\r", "\n" ], ' ', $message );
            \WP_CLI::line( json_encode( $message ) );
        }
    }

    /**
     * Prints a line in command line
     *
     * @param string $message     The message string to print
     * @param bool   $prefix_time Whether to print date & time before the message or not
     */
    public static function printLine( $message, $prefix_time = true )
    {
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            $message = strip_tags( $message );
            $message = str_replace( [ "\r\n", "\n\r", "\r", "\n" ], ' ', $message );
            $message = $prefix_time
                ? date( 'Y-m-d H:i:s' ) . ' ' . $message
                : $message;
            \WP_CLI::line( \WP_CLI::colorize( $message ) );
        }
    }

    /**
     * Prints an error line in command line
     *
     * @param string $message     The message string to print
     * @param bool   $prefix_time Whether to print date & time before the message or not
     */
    public static function printError( $message, $prefix_time = true )
    {
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            $message = strip_tags( $message );
            $message = str_replace( [ "\r\n", "\n\r", "\r", "\n" ], ' ', $message );
            $message = $prefix_time
                ? date( 'Y-m-d H:i:s' ) . ' ' . $message
                : $message;
            \WP_CLI::error( \WP_CLI::colorize( $message ) );
        }
    }

    /**
     * Prints a warning line in command line
     *
     * @param string $message     The message string to print
     * @param bool   $prefix_time Whether to print date & time before the message or not
     */
    public static function printWarning( $message, $prefix_time = true )
    {
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            $message = strip_tags( $message );
            $message = str_replace( [ "\r\n", "\n\r", "\r", "\n" ], ' ', $message );
            $message = $prefix_time
                ? date( 'Y-m-d H:i:s' ) . ' ' . $message
                : $message;
            \WP_CLI::warning( \WP_CLI::colorize( $message ) );
        }
    }

    /**
     * Prints a success line in command line
     *
     * @param string $message     The message string to print
     * @param bool   $prefix_time Whether to print date & time before the message or not
     */
    public static function printSuccess( $message, $prefix_time = true )
    {
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            $message = strip_tags( $message );
            $message = str_replace( [ "\r\n", "\n\r", "\r", "\n" ], ' ', $message );
            $message = $prefix_time
                ? date( 'Y-m-d H:i:s' ) . ' ' . $message
                : $message;
            \WP_CLI::success( \WP_CLI::colorize( $message ) );
        }
    }
}
