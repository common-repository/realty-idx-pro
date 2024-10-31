<?php

use IDXRealtyPro\Model\Api;

if ( ! defined( 'ABSPATH' ) ) {
	die( "You are not allowed to call this page directly." );
}
/**
 * @var array $instance The widget options
 */

$title          = ! empty( $instance['title'] ) ? $instance['title'] : '';
$col_num        = ! empty( $instance['col_num'] ) ? $instance['col_num'] : '';
$server_id      = ! empty( $instance['server_id'] ) ? $instance['server_id'] : '';
$resource_class = ! empty( $instance['resource_class'] ) ? (array) $instance['resource_class'] : [];
$field_name     = ! empty( $instance['field_name'] ) ? $instance['field_name'] : '';
$include        = ! empty( $instance['include'] ) ? implode( ',', $instance['include'] ) : '';
$servers_list   = Api::getServers();
$server         = ! empty( $server_id ) ? wp_filter_object_list( $servers_list, [ 'server_id' => $server_id ] ) : '';
$server         = ! empty( $server ) ? call_user_func_array( 'array_merge', $server ) : $server;
$server_key     = isset( $server['server_key'] ) ? $server['server_key'] : '';
?>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php
		esc_attr_e( 'Title:', 'realty-idx-pro' ); ?></label>
	<input
		class="widefat"
		id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
		name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
		type="text"
		value="<?php echo esc_attr( $title ); ?>"
	/>
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'server_id' ) ); ?>"><?php
		esc_attr_e( 'Select Server', 'realty-idx-pro' ); ?></label>
	<?php if ( is_wp_error( $servers_list ) ) : ?>
		<span class="notice-error notice"><?php echo $servers_list->get_error_message(); ?></span>
	<?php else : ?>
		<select
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'server_id' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'server_id' ) ); ?>"
		>
			<option value=""><?php _e( '- Select -' ) ?></option>
			<?php
			foreach ( $servers_list as $server ) {
				?>
				<option
					value="<?php echo $server['server_id']; ?>"<?php selected(
					$server_id,
					$server['server_id']
				); ?>><?php
					echo esc_html( "{$server['name']}" ); ?></option>
				<?php
			}
			?>
		</select>
	<?php endif; ?>
</p>
<?php if ( $server_id ) : ?>
	<input
		type="hidden"
		name="<?php echo esc_attr( $this->get_field_name( 'server_key' ) ); ?>"
		value="<?php esc_attr_e( $server_key ); ?>"
	/>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'resource_class' ) ); ?>"><?php
			esc_attr_e( 'Select Resource:Class', 'realty-idx-pro' ); ?></label>
		<select
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'resource_class' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'resource_class' ) ); ?>[]"
			multiple="multiple"
			size="8"
		>
			<?php
			$classes = Api::getResourceClasses( $server['server_id'] );
			foreach ( $classes as $_resource_class ) {
				if ( empty( $_resource_class['resource_id'] ) || empty( $_resource_class['class_name'] ) ) {
					continue;
				}
				$resource_class_value = sprintf(
					'%s:%s',
					esc_attr( $_resource_class['resource_id'] ),
					esc_attr( $_resource_class['class_name'] )
				);
				?>
				<option
					value="<?php echo $resource_class_value; ?>"
					<?php selected( in_array( $resource_class_value, $resource_class ) ); ?>
				><?php esc_html_e(
						"{$_resource_class['resource_id']}:{$_resource_class['class_name']}"
					); ?></option>
				<?php
			}
			?>
		</select>
		<small><?php printf(
				__(
					'Press and hold %1$s[ctrl]%2$s or %1$s[cmd]%2$s and click to select multiple options.',
					'realty-idx-pro'
				),
				'<code>',
				'</code>'
			); ?></small>
	</p>
<?php endif; ?>
<?php if ( $resource_class ) : ?>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'field_name' ) ); ?>"><?php
			esc_attr_e( 'Field Name', 'realty-idx-pro' ); ?></label>
		<select
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'field_name' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'field_name' ) ); ?>"
		>
			<?php
			$field_options = [];
			foreach ( $resource_class as $rc ) {
				list( $resource_id, $class_name ) = explode( ':', $rc );
				$class_fields = Api::getClassFields( $server_id, $resource_id, $class_name );
				if ( ! is_wp_error( $class_fields ) && ! empty( $class_fields['fields'] ) ) {
					foreach ( $class_fields['fields'] as $field ) {
						if ( isset( $field_options[ $field['system_name'] ] ) &&
						     false === stripos( $field_options[ $field['system_name'] ], $field['long_name'] ) ) {
							$field_options[ $field['system_name'] ] .= '/' . $field['long_name'];
						} else {
							$field_options[ $field['system_name'] ] = $field['long_name'];
						}
					}
				}
			}
			?>
			<?php if ( ! empty( $field_options ) ) :
				foreach ( $field_options as $_field_name => $_field_label ) :
					?>
					<option
						value="<?php esc_attr_e( $_field_name ); ?>"<?php selected(
						$field_name,
						$_field_name
					); ?>><?php esc_attr_e( $_field_label ); ?></option>
				<?php
				endforeach;
			endif;
			?>
		</select>
		<small><?php _e( 'Select the field name of the values to enter.', 'realty-idx-pro' ); ?>/small>
	</p>
	<?php if ( $field_name ) : ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'include' ) ); ?>"><?php
				esc_attr_e( 'Listings to inlude', 'realty-idx-pro' ); ?></label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'include' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'include' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $include ); ?>"
			/>
			<small><?php _e( 'Enter a comma separated values.', 'realty-idx-pro' ); ?></small>
		</p>
	<?php endif; ?>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'col_num' ) ); ?>"><?php
			esc_attr_e( 'Number of Columns', 'realty-idx-pro' ); ?></label>
		<input
			class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'col_num' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'col_num' ) ); ?>"
			type="number"
			min="1"
			max="12"
			value="<?php echo esc_attr( $col_num ); ?>"
		/>
		<small><?php _e( 'Enter the number of columns to display per row.', 'realty-idx-pro' ); ?></small>
	</p>
<?php endif; ?>
