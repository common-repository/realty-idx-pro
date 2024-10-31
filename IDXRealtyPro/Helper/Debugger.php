<?php
namespace IDXRealtyPro\Helper;

if ( ! defined( 'ABSPATH' ) || preg_match(
        '#' . basename( __FILE__ ) . '#',
        $_SERVER['PHP_SELF']
    )
) {
    die( "You are not allowed to call this page directly." );
}

class Debugger
{

    const LOG = 1;

    const INFO = 2;

    const WARN = 3;

    const ERROR = 4;

    const NL = "\r\n";

    private static $debug_key = 'IDXRealtyPro';

    public static function ipMatched( $ip )
    {
        if ( strpos( self::getClientIp(), $ip ) !== false ) {
            return true;
        }

        return false;
    }

    /**
     * Get the client's IP Address.
     *
     * @param bool $proxy Whether to include the proxy ip address or not.
     *
     * @return string|\stdClass If $proxy is true, returns an object with properties $ip and includes $proxy if it is
     * set. Otherwise, returns $ip as string.
     */
    public static function getClientIp( $proxy = false )
    {
        if ( $_SERVER["HTTP_X_FORWARDED_FOR"] ) {
            if ( $_SERVER["HTTP_CLIENT_IP"] ) {
                $proxy = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $proxy = $_SERVER["REMOTE_ADDR"];
            }
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            if ( $_SERVER["HTTP_CLIENT_IP"] ) {
                $ip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $ip = $_SERVER["REMOTE_ADDR"];
            }
        }

        if ( $proxy ) {
            $obj     = new \stdClass();
            $obj->ip = $ip;
            if ( isset( $proxy ) ) {
                $obj->proxy = $proxy;
            }

            return $obj;
        }

        return $ip;
    }

    /**
     * Dump all functions hooked or hooked to a tag.
     *
     * @param bool|string $tag False to dump all hooked functions, the hook tag to dump all functions hooked to that
     *                         specific hook tag.
     */
    public static function listHookedFunctions( $tag = false )
    {
        global $wp_filter;
        if ( $tag ) {
            $hook[ $tag ] = $wp_filter[ $tag ];
            if ( ! is_array( $hook[ $tag ] ) ) {
                trigger_error( "Nothing found for '$tag' hook", E_USER_WARNING );

                return;
            }
        } else {
            $hook = $wp_filter;
            ksort( $hook );
        }
        echo '<pre>';
        foreach ( $hook as $tag => $priority ) {
            echo "<br />&gt;&gt;&gt;&gt;&gt;\t<strong>$tag</strong><br />";
            ksort( $priority );
            foreach ( $priority as $priority => $function ) {
                echo $priority;
                foreach ( $function as $name => $properties ) {
                    echo "\t$name<br />";
                }
            }
        }
        echo '</pre>';

        return;
    }

    /**
     * Creates an html page with printed variable dumped.
     *
     * @param mixed  $var       An array or object to be dumped.
     * @param string $file_name The name of the output file.
     * @param string $path
     */
    public static function printHtml( $var, $file_name, $path = "debug-files" )
    {
        $var  = "<pre>" . print_r( $var, true ) . "</pre>";
        $dump = <<<EOD
<html>
    <head>
    <title>$file_name Dump</title>
    </head>
    <body>$var</body>
</html>
EOD;
        $path = ABSPATH . $path;
        if ( ! file_exists( $path ) ) {
            @mkdir( $path, 0755, true );
        }
        $file = $path . "/$file_name.html";
        $filehandle = fopen( $file, 'w' ) or exit( 'Unable to write/open file.' );
        @fwrite( $filehandle, $dump );
        @fclose( $filehandle );
        chmod( $file, 0664 );
    }

    /**
     * Creates a file with printed variable dump which can be appended into or overwritten.
     *
     * @param mixed  $var       The variable to dump
     * @param bool   $append    Whether to append to the file if it exists or overwrite the file
     * @param string $file_name The name of the output file.
     * @param string $path      The path to the file
     */
    public static function debugLog( $var, $append = true, $file_name = 'debug.log', $path = 'debug-files' )
    {
        $path = ABSPATH . $path;
        if ( ! file_exists( $path ) ) {
            @mkdir( $path, 0755, true );
        }
        $file = $path . "/$file_name";
        if ( ! $append && file_exists( $file ) ) {
            unlink( $file );
        }
        file_put_contents( $file, print_r( $var, true ), FILE_APPEND );
    }

    /**
     * Dump passed variables and print it enclosed in &lt;pre&gt; tags.
     *
     * @param mixed $vars
     * @param null  $_ [optional]
     */
    public static function printPre( $vars, $_ = null )
    {
        $vars = func_get_args();

        $html = '';
        foreach ( $vars as $var ) {
            $html .= "<pre>" . print_r( $var, true ) . "</pre>";
        }

        echo $html;
    }

    /**
     * Dump each variable passed and return dump enclosed in &lt;pre&gt; tags.
     *
     * @param mixed $vars
     * @param null  $_ [optional]
     *
     * @return string Dumped variable enclosed in &lt;pre&gt; tags.
     */
    public static function getPrintPre( $vars, $_ = null )
    {
        $vars = func_get_args();

        $html = '';
        foreach ( $vars as $var ) {
            $html .= "<pre>" . print_r( $var, true ) . "</pre>";
        }

        return $html;
    }

    /**
     * Dump variable and save it in options db table. Variables added into the
     * table are dumped in the next request and emptied. You must hook debug_dump
     * to an action hook/filter (e.g. preferably 'admin_notices')
     *
     * @see  Debugger::debugDump()
     *
     * @param null $var The variable to be dumped.
     */
    public static function adminDebugVar( $var = null )
    {
        $errors = get_option( self::$debug_key, [ ] );
        if ( $var !== null ) {
            $errors[] = self::getPrintPre( $var );
            update_option( self::$debug_key, $errors );
        }
    }

    /**
     * For use in an action hook/filter (e.g. 'admin_notices') for printing of dumped variables.98uy
     */
    public static function debugDump()
    {
        $errors = get_option( self::$debug_key );
        if ( $errors && is_array( $errors ) ) {
            ?>
            <div class='updated' id='message'>
                <h3>DEBUG DUMP</h3>
                <?php
                foreach ( $errors as $error ) {
                    ?>
                    <?php echo $error; ?>
                    <?php
                }
                ?>
            </div>
            <?php
            update_option( self::$debug_key, [ ] );
        }
    }

    public static function consoleDebugDump( $name, $var = null, $type = self::LOG )
    {
        echo '<script type="text/javascript">' . self::NL;
        switch ( $type ) {
            case self::LOG:
                echo 'console.log("' . $name . '");' . self::NL;
                break;
            case self::INFO:
                echo 'console.info("' . $name . '");' . self::NL;
                break;
            case self::WARN:
                echo 'console.warn("' . $name . '");' . self::NL;
                break;
            case self::ERROR:
                echo 'console.error("' . $name . '");' . self::NL;
                break;
        }

        if ( ! empty( $var ) ) {
            if ( is_object( $var ) || is_array( $var ) ) {
                $object = json_encode( $var );
                echo 'var object' . preg_replace( '~[^A-Z|0-9]~i', "_", $name ) . ' = \'' . str_replace(
                        "'",
                        "\'",
                        $object
                    ) . '\';' . self::NL;
                echo 'var val' . preg_replace(
                        '~[^A-Z|0-9]~i',
                        "_",
                        $name
                    ) . ' = eval("(" + object' . preg_replace(
                         '~[^A-Z|0-9]~i',
                         "_",
                         $name
                     ) . ' + ")" );' . self::NL;
                switch ( $type ) {
                    case self::LOG:
                        echo 'console.debug(val' . preg_replace( '~[^A-Z|0-9]~i', "_", $name ) . ');' . self::NL;
                        break;
                    case self::INFO:
                        echo 'console.info(val' . preg_replace( '~[^A-Z|0-9]~i', "_", $name ) . ');' . self::NL;
                        break;
                    case self::WARN:
                        echo 'console.warn(val' . preg_replace( '~[^A-Z|0-9]~i', "_", $name ) . ');' . self::NL;
                        break;
                    case self::ERROR:
                        echo 'console.error(val' . preg_replace( '~[^A-Z|0-9]~i', "_", $name ) . ');' . self::NL;
                        break;
                }
            } else {
                switch ( $type ) {
                    case self::LOG:
                        echo 'console.debug("' . str_replace( '"', '\\"', $var ) . '");' . self::NL;
                        break;
                    case self::INFO:
                        echo 'console.info("' . str_replace( '"', '\\"', $var ) . '");' . self::NL;
                        break;
                    case self::WARN:
                        echo 'console.warn("' . str_replace( '"', '\\"', $var ) . '");' . self::NL;
                        break;
                    case self::ERROR:
                        echo 'console.error("' . str_replace( '"', '\\"', $var ) . '");' . self::NL;
                        break;
                }
            }
        }
        echo '</script>' . self::NL;
    }

    /**
     * Prints passed variables to footer
     *
     * @param mixed $vars ...
     * @param null  $_    [optional]
     */
    public static function printToFooter( $vars, $_ = null )
    {
        $vars = func_get_args();

        if ( is_admin() ) {
            $tag = 'admin_footer';
        } else {
            $tag = 'wp_footer';
        }

        add_action(
            $tag,
            function () use ( $tag, $vars ) {
                foreach ( $vars as $var ) {
                    if ( 'admin_footer' === $tag ) {
                        printf(
                            '<div class="debugger" style="margin-left: 160px; padding: 1rem;">%s</div>',
                            Debugger::getPrintPre( $var )
                        );
                    } else {
                        printf(
                            '<div class="debugger">%s</div>',
                            Debugger::getPrintPre( $var )
                        );
                    }
                }
            }
        );
    }
}
