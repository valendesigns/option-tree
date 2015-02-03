<?php
if ( ! class_exists( 'OT_Widgets_API' ) ) {

  class OT_Widgets_API {
    /**
     * Container for all registered OT_Widget class names
     * @var array
     */
    static private $widgets = array();

    /**
     * Simple class constructor
     */
    function __construct() {
      // Register saved widgets
      add_action( 'widgets_init', array( $this, 'register_widgets' ) );
    }

    /**
     * Adds OT_Widget classname to our container
     * @param  string $classname Class name
     * @return void
     */
    public static function register_widget( $classname ) {
      self::$widgets[] = $classname;
    }

    /**
     * Registers all widgets with WordPress
     * @return void
     */
    function register_widgets() {
      foreach( self::$widgets as $widget_class ) {
        if( class_exists( $widget_class ) )
          register_widget( $widget_class );
      }
    }
  }

  $ot_widgets_api = new OT_Widgets_API();
}

if( ! class_exists( 'OT_Widget' ) ) {

  abstract class OT_Widget extends WP_Widget {
    public $fields = array();

    function __construct() {
      $widget_ops = array(
        //'classname'   => $this->widget_cssclass,
        'description' => $this->description
      );

      $this->WP_Widget( $this->id, $this->name, $widget_ops );
    }

    public function form( $instance ) {
      if( ! is_array( $this->fields ) || empty( $this->fields ) )
        return;

      foreach( $this->fields as $field ) {
        if( ! isset( $field['id'] ) || ! isset( $field['type'] ) )
          continue;

        $field_id    = $field['id'];
        $field_value = isset( $instance[ $field_id ] ) ? $instance[ $field_id ] : FALSE;
        $field_type  = $field['type'];

        if ( isset( $field['std'] ) ) {
          $field_value = ot_filter_std_value( $field_value, $field['std'] );
        }

        /* build the arguments array */
        $_args = array(
          'type'              => $field['type'],
          'field_id'          => $this->get_field_id( $field['id'] ),
          'field_name'        => $this->get_field_name( $field['id'] ),
          'field_value'       => $field_value,
          'field_desc'        => isset( $field['desc'] ) ? $field['desc'] : '',
          'field_std'         => isset( $field['std'] ) ? $field['std'] : '',
          'field_rows'        => isset( $field['rows'] ) && ! empty( $field['rows'] ) ? $field['rows'] : 10,
          'field_post_type'   => isset( $field['post_type'] ) && ! empty( $field['post_type'] ) ? $field['post_type'] : 'post',
          'field_taxonomy'    => isset( $field['taxonomy'] ) && ! empty( $field['taxonomy'] ) ? $field['taxonomy'] : 'category',
          'field_min_max_step'=> isset( $field['min_max_step'] ) && ! empty( $field['min_max_step'] ) ? $field['min_max_step'] : '0,100,1',
          'field_class'       => isset( $field['class'] ) ? $field['class'] : '',
          'field_condition'   => isset( $field['condition'] ) ? $field['condition'] : '',
          'field_operator'    => isset( $field['operator'] ) ? $field['operator'] : 'and',
          'field_choices'     => isset( $field['choices'] ) ? $field['choices'] : array(),
          'field_settings'    => isset( $field['settings'] ) && ! empty( $field['settings'] ) ? $field['settings'] : array(),
          'post_id'           => 0
        );

        $conditions = '';

        /* setup the conditions */
        if ( isset( $field['condition'] ) && ! empty( $field['condition'] ) ) {

          $conditions = ' data-condition="' . $field['condition'] . '"';
          $conditions.= isset( $field['operator'] ) && in_array( $field['operator'], array( 'and', 'AND', 'or', 'OR' ) ) ? ' data-operator="' . $field['operator'] . '"' : '';

        }

        /* only allow simple textarea due to DOM issues with wp_editor() */
        if ( apply_filters( 'ot_override_forced_textarea_simple', false, $field['id'] ) == false && $_args['type'] == 'textarea' )
          $_args['type'] = 'textarea-simple';

        // Build the setting CSS class
        if ( ! empty( $_args['field_class'] ) ) {

          $classes = explode( ' ', $_args['field_class'] );

          foreach( $classes as $key => $value ) {

            $classes[$key] = $value . '-wrap';

          }

          $class = 'format-settings ' . implode( ' ', $classes );

        } else {

          $class = 'format-settings';

        }

        /* option label */
        echo '<div id="setting_' . $field['id'] . '" class="' . $class . '"' . $conditions . '>';

          echo '<div class="format-setting-wrap">';

            /* don't show title with textblocks */
            if ( $_args['type'] != 'textblock' && ! empty( $field['label'] ) ) {
              echo '<div class="format-setting-label">';
                echo '<label for="' . $this->get_field_id( $field['id'] ) . '" class="label">' . $field['label'] . '</label>';
              echo '</div>';
            }

            /* get the option HTML */
            echo ot_display_by_type( $_args );

          echo '</div>';

        echo '</div>';
      }
    }

    public function update( $new_instance, $old_instance ) {
      $new_data = array();

      foreach ( $this->fields as $field ) {

        $old = isset( $old_instance[$field['id']] ) ? $old_instance[$field['id']] : '';
        $new = '';

        /* there is data to validate */
        if ( isset( $new_instance[$field['id']] ) ) {

          /* slider and list item */
          if ( in_array( $field['type'], array( 'list-item', 'slider' ) ) ) {

            /* required title setting */
            $required_setting = array(
              array(
                'id'        => 'title',
                'label'     => __( 'Title', 'option-tree' ),
                'desc'      => '',
                'std'       => '',
                'type'      => 'text',
                'rows'      => '',
                'class'     => 'option-tree-setting-title',
                'post_type' => '',
                'choices'   => array()
              )
            );

            /* get the settings array */
            $settings = isset( $new_instance[$field['id'] . '_settings_array'] ) ? unserialize( ot_decode( $new_instance[$field['id'] . '_settings_array'] ) ) : array();

            /* settings are empty for some odd ass reason get the defaults */
            if ( empty( $settings ) ) {
              $settings = 'slider' == $field['type'] ?
              ot_slider_settings( $field['id'] ) :
              ot_list_item_settings( $field['id'] );
            }

            /* merge the two settings array */
            $settings = array_merge( $required_setting, $settings );

            foreach( $new_instance[$field['id']] as $k => $setting_array ) {

              foreach( $settings as $sub_setting ) {

                /* verify sub setting has a type & value */
                if ( isset( $sub_setting['type'] ) && isset( $new_instance[$field['id']][$k][$sub_setting['id']] ) ) {

                  $new_instance[$field['id']][$k][$sub_setting['id']] = ot_validate_setting( $new_instance[$field['id']][$k][$sub_setting['id']], $sub_setting['type'], $sub_setting['id'] );

                }

              }

            }

            /* set up new data with validated data */
            $new = $new_instance[$field['id']];

          } else if ( $field['type'] == 'social-links' ) {

            /* get the settings array */
            $settings = isset( $new_instance[$field['id'] . '_settings_array'] ) ? unserialize( ot_decode( $new_instance[$field['id'] . '_settings_array'] ) ) : array();

            /* settings are empty get the defaults */
            if ( empty( $settings ) ) {
              $settings = ot_social_links_settings( $field['id'] );
            }

            foreach( $new_instance[$field['id']] as $k => $setting_array ) {

              foreach( $settings as $sub_setting ) {

                /* verify sub setting has a type & value */
                if ( isset( $sub_setting['type'] ) && isset( $new_instance[$field['id']][$k][$sub_setting['id']] ) ) {

                  $new_instance[$field['id']][$k][$sub_setting['id']] = ot_validate_setting( $new_instance[$field['id']][$k][$sub_setting['id']], $sub_setting['type'], $sub_setting['id'] );

                }

              }

            }

            /* set up new data with validated data */
            $new = $new_instance[$field['id']];

          } else {
            /* run through validattion */
            $new = ot_validate_setting( $new_instance[$field['id']], $field['type'], $field['id'] );
          }

          /* insert CSS */
          if ( $field['type'] == 'css' ) {

            /* insert CSS into dynamic.css */
            if ( '' !== $new ) {

              ot_insert_css_with_markers( $field['id'], $new, true );

            /* remove old CSS from dynamic.css */
            } else {

              ot_remove_old_css( $field['id'] );

            }

          }

        }

        $new_data[$field['id']] = $new;
      }

      return $new_data;
    }
  }
}

if ( ! function_exists( 'ot_register_widget' ) ) {

  function ot_register_widget( $classname ) {
    if( ! $classname )
      return;

    OT_Widgets_API::register_widget( $classname );
  }

}