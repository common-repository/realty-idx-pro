<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( "You are not allowed to call this page directly." );
}
?>
<script id="tmpl-class-select-field" type="text/html">
    <label for="class-select" class="col-4 control-label">{{ idxrp.l10n.select_class }}</label>
    <div class="col-8">
        <select id="class-select">
            <option value="">- Select -</option>
            <# data.forEach( function (r_class, index) { #>
                    <option value="{{ r_class.resource_id }}:{{ r_class.class_name }}">{{ r_class.resource_label }}:{{ r_class.class_name }} ({{ r_class.class_label }})</option>
            <# } ) #>
        </select>
    </div>
</script>
<script id="tmpl-rets-select-field" type="text/html">
    <label for="field-select" class="col-4 control-label">{{ idxrp.l10n.select_field }}</label>
    <div class="col-8">
        <select id="field-select">
            <option value="">- Select -</option>
            <# data.fields.forEach( function (field, index) { #>
                    <option value="{{ field.system_name }}">{{ field.long_name }}</option>
            <# } ) #>
        </select>
    </div>
</script>

