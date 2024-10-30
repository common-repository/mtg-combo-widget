<?php
/*
Plugin Name: M:tG combo widget
Description: M:tG combo widget, display combo.magicology.nl combos
Author: Joep van Abeelen
Version: 1.3
Author URI: http://magicology.nl/
*/
 
 
class MtgComboWidget extends WP_Widget
{
  function MtgComboWidget()
  {
    $widget_ops = array('classname' => 'MtgComboWidget', 'description' => 'Displays M:tG combos' );
    $this->WP_Widget('MtgComboWidget', 'M:tG combo widget', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
    $count = $instance['count'];
    $card = $instance['card'];
    $showimg = $instance['showimg'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
  <p>
    <label for="<?php echo $this->get_field_id('count'); ?>">
        Show 
        <select id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>">
            <option value="1" <?php if($count==1) echo('selected="selected"'); ?>>1</option>
            <option value="2" <?php if($count==2) echo('selected="selected"'); ?>>2</option>
            <option value="3" <?php if($count==3) echo('selected="selected"'); ?>>3</option>
            <option value="4" <?php if($count==4) echo('selected="selected"'); ?>>4</option>
            <option value="5" <?php if($count==5) echo('selected="selected"'); ?>>5</option>
        </select>
        combos
    </label>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id('showimg'); ?>">
        Show 
        <select id="<?php echo $this->get_field_id('showimg'); ?>" name="<?php echo $this->get_field_name('showimg'); ?>">
            <option value="1" <?php if($showimg==1) echo('selected="selected"'); ?>>images</option>
            <option value="2" <?php if($showimg==2) echo('selected="selected"'); ?>>only text</option>
        </select>
    </label>
  </p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['count'] = $new_instance['count'];
    $instance['card'] = $new_instance['card'];
    $instance['showimg'] = $new_instance['showimg'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
    
    
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $count = empty($instance['count']) ? 1 : (int) $instance['count'];
    $card = empty($instance['card']) ? '' : $instance['card'];
    if($instance['showimg']=='0'||$instance['showimg']=='false') $showimg=0;
    else $showimg = 1;
    $rand=rand(100000,1000000);
 
    // WIDGET CODE GOES HERE
    echo '<div class="comboboxcontainer">
            <div class="comboboxheader">'.(!empty($title)?$before_title . $title . $after_title:'').'</div>
            <div id="mtg-combo-widget-combobox-'.$rand.'" class="combobox">Loading...</div>
        </div>
        <div style="clear:both;"></div>
        <link rel="stylesheet" href="http://combo2.magicology.nl/jsapi.css" media="screen" type="text/css" />
        <script type="text/javascript">
            var cbox'.$rand.' = new comboapi.comboTable();
            cbox'.$rand.'.num='.$count.';
            cbox'.$rand.'.card="'.$card.'";
            cbox'.$rand.'.showCards = '.($showimg==1?'true':'false').';
            cbox'.$rand.'.draw("mtg-combo-widget-combobox-'.$rand.'");
        </script>';
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("MtgComboWidget");') );
addHeaderCode();
function addHeaderCode() {
    if (function_exists('wp_enqueue_script')) {
        wp_enqueue_script('mtg-combo-widget-script', 'http://combo2.magicology.nl/jsapi.js');
    }
    return;
}


function combolist_shortcode($atts) {
    
    global $wp_widget_factory;
    
    extract(shortcode_atts(array(
        'title' => FALSE,
	'count' => 3,
	'showimg' => 1,
        'card' => FALSE
    ), $atts));
    
    $widget_name = 'MtgComboWidget';
    
    if (!is_a($wp_widget_factory->widgets[$widget_name], 'WP_Widget')):
        $wp_class = 'WP_Widget_'.ucwords(strtolower($class));
        if (!is_a($wp_widget_factory->widgets[$wp_class], 'WP_Widget')):
            return ''; 
        else:
            $class = $wp_class;
        endif;
    endif;
    
    ob_start();
    the_widget($widget_name, $atts, array('widget_id'=>'arbitrary-instance-'.$id,
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
    
}
add_shortcode('combolist','combolist_shortcode');
?>