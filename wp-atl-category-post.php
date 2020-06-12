<?php
/*
Plugin Name: Посты рубрик
Description: Плагин виджета вывода постов рубрик
Version: 1.0
Author: Atlas-it
Author URI: http://atlas-it.by
*/

/*Регистрация виджета*/
add_action('widgets_init', 'wp_atl_category_post');
function wp_atl_category_post () { 
    register_widget('atl_category_post');
}

class atl_category_post extends WP_Widget {
 
    public function __construct() {
    $args = array (
        'name'=>'Посты рубрик',
        'description'=>'Виджет вывода постов рубрик'
         );
        parent::__construct ('wp_atl_category_post', '', $args);
    }
    
    public function form ($instance) {
        $parent_id = isset($instance['id_parent']) ? $instance['id_parent']:'';  
        $title = isset($instance['title']) ? $instance['title']:'Записи рубрики';
        $postsnumber = isset($instance['postsnumber']) && !empty($instance['postsnumber']) ? $instance['postsnumber']: 5;
        
        ?> 
            <p>
                <label for = "<?php echo $this->get_field_id('title');?>">Заголовок</label>
                <input class="widefat title" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" value="<?php echo $title;?>">
            </p>
            <p>
                <label for = "<?php echo $this->get_field_id('id_parent');?>">Выберите рубрику</label>
                    <select class = "widefat" id="<?php echo $this->get_field_id('id_parent');?>" name="<?php echo $this->get_field_name('id_parent');?>">
                            <option></option>
                    <?php 
                            $args = array (
                                    'type'         => 'post',
                                    'hide_empty'   => 1,
                                    'pad_counts' => 1,
                                    'hierarchical' => 1,
                                    'number'       => 0
                                    );
                                $all_categories = get_categories($args);
                                if($all_categories) {
                                    foreach ($all_categories as $category) {
                                        if ($category->term_id == $parent_id ) {echo '<option value ='.$category->term_id.' selected="selected">'.$category->name.'</option>';}
                                        else {echo '<option value ='.$category->term_id.'>'.$category->name.'</option>';}
                                    }
                                } ?>
                    </select>
            </p>
            <p>
                <label for = "<?php echo $this->get_field_id('postsnumber');?>">Количество записей</label>
                <input class="widefat title" id="<?php echo $this->get_field_id('postsnumber');?>" name="<?php echo $this->get_field_name('postsnumber');?>" value="<?php echo $postsnumber;?>">
            </p>

            <p>
                <?php
                   
                        if(isset($instance['date_post'])  && $instance['date_post'] == 'on'){
                            echo '<input type="checkbox" id="'.$this->get_field_id('date_post').'" name="'.$this->get_field_name('date_post').'" value ="on" checked>
                                  <label for="'.$this->get_field_id('date_post').'">Показывать дату последнего изменения поста</label><br />';
                        }else echo '<input type="checkbox" id="'.$this->get_field_id('date_post').'" name="'.$this->get_field_name('date_post').'" value ="on">
                                  <label for="'.$this->get_field_id('date_post').'">Показывать дату последнего изменения поста</label><br />';
                  
                ?>           
            </p>
            <?php
               
                 }

    public function widget ($args, $instance) { 
                                   //Запрос вывода постов определенной категории
                                        $posts = get_posts( array(
                                            'numberposts' => $instance['postsnumber'],
                                            'post_type' => 'post',
                                            'post_status' => 'publish',
                                            'category' => $instance['id_parent'],
                                            'orderby' => 'date',
                                        ) ); 
        
                /*Вывод списка дочерних страниц*/
                echo $args['before_widget'];
                echo $args['before_title'].$instance['title'].$args['after_title'];   
                ?>
                    <ul>
                        <?php  if ($posts) {
                                    foreach ($posts as $post) {  
                                        if (isset($instance['date_post']) && $instance['date_post'] == 'on' ) {            
                            ?>
                                        <li><a href="<?php echo get_permalink($post->ID);?>" ><?php echo $post->post_title; ?> (<?php echo get_the_modified_date('Y-m-d', $post->ID); ?>)</a></li>
                        <?php  }else {?> 
                                    <li><a href="<?php echo get_permalink($post->ID);?>" ><?php echo $post->post_title; ?></a></li>
                        <?php }} }
                                else  { ?>
                                        <li>Нет записей выбранной категории</li>
                               <?php }; ?>
                    </ul>
                <?php   
                echo $args['after_widget'];
    }  
    
    public function update ($new_instance, $old_instance) {
        $new_instance['postsnumber'] = isset($new_instance['postsnumber']) && !empty($new_instance['postsnumber']) ? $new_instance['postsnumber']: 5;
        $new_instance['title']=!empty($new_instance['title'])?strip_tags($new_instance['title']):'Каталог';
        
        return $new_instance;
    }
}