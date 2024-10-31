/**
 * Author: Paul Grejaldo
 * Date: 2017/02/27
 * Time: 10:39 PM
 */
(function ( $ ) {
    "use strict";

    var idxrpQt = {
        server_id        : 0,
        rets_fields_data : {},
        selected_field   : '',
        template_server  : 0,
        template_type    : '',

        init : function () {
            var self = this,
                qtButtons = [
                    {
                        id       : 'idxrp_qt_list_template',
                        display  : 'idxrp list',
                        callback : function ( el ) {
                            idxrpQt.insertTemplate( 'list-view', 0 );
                        },
                        title    : idxrp.l10n.list
                    },
                    {
                        id       : 'idxrp_qt_photo_template',
                        display  : 'idxrp photo',
                        callback : function ( el ) {
                            idxrpQt.insertTemplate( 'photo-view', 0 );
                        },
                        title    : idxrp.l10n.photo
                    },
                    {
                        id       : 'idxrp_qt_map_photo_template',
                        display  : 'idxrp map',
                        callback : function ( el ) {
                            idxrpQt.insertTemplate( 'map-view', 0 );
                        },
                        title    : idxrp.l10n.map
                    },
                    {
                        id       : 'idxrp_qt_marker_template',
                        display  : 'idxrp marker',
                        callback : function ( el ) {
                            idxrpQt.insertTemplate( 'marker-info', 0 );
                        },
                        title    : idxrp.l10n.marker
                    },
                    {
                        id       : 'idxrp_qt_single_template',
                        display  : 'idxrp single',
                        callback : function ( el ) {
                            self.template_type = 'single';
                            self.load_template();
                        },
                        title    : idxrp.l10n.single
                    },
                    {
                        id       : 'idxrp_qt_single_marker_template',
                        display  : 'idxrp single marker',
                        callback : function ( el ) {
                            self.template_type = 'single-marker';
                            self.load_template();
                        },
                        title    : idxrp.l10n.single
                    },
                    {
                        id       : 'rets-fields',
                        display  : 'rets fields',
                        callback : function ( el ) {
                            self.rets_fields();
                        },
                        title    : 'RETS Fields'
                    },
                ];

            $( qtButtons ).each( function ( i, button ) {
                QTags.addButton( button.id, button.display, button.callback, '', '', button.title, 200 + i );
            } );

            $( '.idxrp-rets-fields-dialog' )
                .modal( { show : false } )
                .on( 'show.bs.modal', self.repositionModal )
                .on( 'change', '#class-select', self.changeClass )
                .on( 'change', '#field-select', self.changeRetsField )
                .on( 'click', '#insert-rets-field', self.insertRetsField )
                .on( 'click', '#cancel-insert-rets-field', self.cancelInsertRetsField );
            // Reposition when the window is resized
            $( window ).on( 'resize', function () {
                $( '.idxrp-rets-fields-dialog' ).each( self.repositionModal );
            } );
            $( '#idxrp-server-select' ).on( 'change', self.changeServer );

            $( '.idxrp-rets-server-dialog' )
                .modal( { show : false } )
                .on( 'show.bs.modal', self.repositionModal )
                .on( 'change', '#rets-server-select', self.changeTemplateServer )
                .on( 'click', '#insert-template', self.loadTemplate )
                .on( 'click', '#cancel-insert-template', self.cancelLoadTemplate );
        },

        insertTemplate : function ( template, server_id ) {
            $( '.loading-template' ).show();
            this.ajax( { template : template, server_id : server_id, ep : 'idxrp_load_template' } )
                .then(
                    function ( data, textStatus, jqXHR ) {
                        if ( data ) {
                            QTags.insertContent( data );
                            $( '.idxrp-rets-server-dialog' ).modal( 'hide' );
                            $( '.loading-template' ).hide();
                        } else {
                            alert( idxrp.l10n.empty );
                        }
                    },
                    function ( jqXHR, textStatus, error ) {
                        console.log( error );
                        $( '.loading-template' ).hide();
                    }
                )
        },

        rets_fields : function () {
            $( '.idxrp-rets-fields-dialog' ).modal( 'toggle' );
        },

        load_template : function () {
            $( '.idxrp-rets-server-dialog' ).modal( 'toggle' );
        },

        changeTemplateServer : function ( e ) {
            idxrpQt.template_server = e.target.value;
        },

        loadTemplate : function ( e ) {
            idxrpQt.insertTemplate( idxrpQt.template_type, idxrpQt.template_server );
        },

        cancelLoadTemplate : function ( e ) {
            $( '.idxrp-rets-server-dialog' ).modal( 'hide' );
        },

        repositionModal : function () {
            var modal = $( this ),
                dialog = modal.find( '.modal-dialog' );
            modal.css( 'display', 'block' );

            // Dividing by two centers the modal exactly, but dividing by three
            // or four works better for larger screens.
            dialog.css( "margin-top", Math.max( 0, ($( window ).height() - dialog.height()) / 2 ) );
        },

        insertRetsField : function ( e ) {
            var tag_type = $( 'input[name="tag_type"]:checked' ).val(),
                tag;
            switch ( tag_type ) {
                case 'js':
                    tag = 'post.' + idxrpQt.selected_field;
                    break;
                case 'template':
                    tag = '{$' + idxrpQt.selected_field + '}';
                    break;
                case 'raw':
                default:
                    tag = idxrpQt.selected_field;
            }

            if ( idxrpQt.selected_field ) {
                QTags.insertContent( tag );
                $( '.idxrp-rets-fields-dialog' ).modal( 'hide' );
            } else {
                alert( idxrp.l10n.no_field );
            }
        },

        cancelInsertRetsField : function ( e ) {
            $( '.idxrp-rets-fields-dialog' ).modal( 'hide' );
        },

        changeServer : function ( e ) {
            var $class_options = $( '.classes-options' );
            idxrpQt.server_id = e.target.value;
            if ( !idxrpQt.server_id ) {
                $class_options.empty();
                $( '.rets-fields-options' ).empty();
                $( '.tag-options' ).hide();
                return;
            }
            if ( !idxrpQt.rets_fields_data[e.target.value] || '' === $class_options.html() ) {
                $class_options.html( '<p>' + idxrp.l10n.loading + '</p>' );
                idxrpQt.ajax( { server_id : e.target.value, ep : 'change_server' } )
                       .then(
                           function ( data, textStatus, jqXHR ) {
                               if ( data ) {
                                   var template = wp.template( 'class-select-field' );
                                   $( '.classes-options' ).html( template( data ) );
                                   idxrpQt.rets_fields_data[e.target.value] = data;
                               } else {
                                   alert( textStatus );
                               }
                           },
                           function ( jqXHR, textStatus, error ) {
                               console.log( jqXHR, error );
                               alert( jqXHR.responseJSON.message );
                           }
                       )
            }
        },

        changeClass : function ( e ) {
            if ( !e.target.value ) {
                $( '.rets-fields-options' ).empty();
                $( '.tag-options' ).hide();
                return;
            }

            var server_id = idxrpQt.server_id,
                r_class = e.target.value.split( ':' ),
                resource_id = r_class[0],
                class_name = r_class[1],
                template = wp.template( 'rets-select-field' ),
                $class_options = $( '.classes-options' );

            $class_options.html( '<p>' + idxrp.l10n.loading + '</p>' );
            idxrpQt.ajax( {
                server_id   : server_id,
                resource_id : resource_id,
                class_name  : class_name,
                ep          : 'change_server'
            } )
                   .then(
                       function ( data, textStatus, jqXHR ) {
                           if ( data ) {
                               $class_options.html( template( data ) );
                               //idxrpQt.rets_fields_data[e.target.value] = data;
                           } else {
                               alert( textStatus );
                           }
                       },
                       function ( jqXHR, textStatus, error ) {
                           console.log( jqXHR, error );
                           alert( jqXHR.responseJSON.message );
                       }
                   );

            /*if ( undefined !== class_data ) {
                $( '.rets-fields-options' ).html( template( class_data ) );
            }*/
        },

        changeRetsField : function ( e ) {
            var $tag_options = $( '.tag-options' );
            idxrpQt.selected_field = e.target.value;
            if ( !e.target.value ) {
                $tag_options.hide();
            } else {
                $tag_options.show();
            }
        },

        ajax : function ( data, type, data_type ) {
            if ( typeof type === 'undefined' ) {
                type = 'post';
            }

            if ( typeof data_type === 'undefined' ) {
                data_type = 'json';
            }

            data = data || {};

            return $.ajax(
                {
                    data        : 'json' === data_type ? JSON.stringify( data ) : data,
                    dataType    : data_type,
                    url         : idxrp.ajaxurl + data.ep,
                    type        : type,
                    contentType : "application/json",
                    headers     : { 'X-WP-Nonce' : idxrp.rest_nonce },
                    timeout     : 30000
                }
            );
        },
    };

    $( function () {
        idxrpQt.init();
    } );
})( jQuery );
