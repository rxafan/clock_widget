<?php

class Clock_info_widget extends WP_Widget
{

    /**
     * Установка идентификатора, заголовка, имени класса и описания для виджета.
     */
    public function __construct()
    {
        $widget_options = array(
            'classname' => 'clock_widget',
            'description' => 'Часовой регион с контактными данными',
        );
        parent::__construct('clock_widget', 'Часовой регион', $widget_options);
    }

    /**
     * Вывод виджета в области виджетов на сайте.
     *
     * @param array $args
     * @param array $instance
     * @throws Exception
     */
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);

        $city = !empty($instance['city']) ? $instance['city'] : '';
        $phone = !empty($instance['phone']) ? $instance['phone'] : '';
        $email = !empty($instance['email']) ? $instance['email'] : '';

        echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];

        $timeOffset = $instance['region'];

        include(locate_template('/inc/template.php', false, false));

        echo $args['after_widget'];
    }

    /**
     * Параметры виджета, отображаемые в области администрирования WordPress.
     *
     * @param array $instance
     * @return string|void
     * @throws Exception
     */
    public function form($instance)
    {
        $region = !empty($instance['region']) ? $instance['region'] : '';
        $city = !empty($instance['city']) ? $instance['city'] : '';
        $phone = !empty($instance['phone']) ? $instance['phone'] : '';
        $email = !empty($instance['email']) ? $instance['email'] : '';
        ?>
        <p>
            <label for="<?= $this->get_field_id('region'); ?>">Часовой регион:</label>
            <select id='<?= $this->get_field_id('region'); ?>' name='<?= $this->get_field_name('region'); ?>' class='widefat'><?= $this->generate_timezone_list($region) ?></select>
        </p>
        <p>
            <label for="<?= $this->get_field_id('city'); ?>">Город:</label>
            <input type="text" value="<?= esc_attr($city); ?>" name="<?= $this->get_field_name('city'); ?>" id="<?= $this->get_field_id('city'); ?>" class="widefat">
        </p>
        <p>
            <label for="<?= $this->get_field_id('phone'); ?>">Телефон:</label>
            <input type="tel" value="<?= esc_attr($phone); ?>" name="<?= $this->get_field_name('phone'); ?>" id="<?= $this->get_field_id('phone'); ?>" class="widefat">
        </p>
        <p>
            <label for="<?= $this->get_field_id('email'); ?>">Е-mail:</label>
            <input type="email" value="<?= esc_attr($email); ?>" name="<?= $this->get_field_name('email'); ?>" id="<?= $this->get_field_id('email'); ?>" class="widefat">
        </p>
        <?php
    }

    /**
     * Обновление настроек виджета в админ-панели.
     *
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['region'] = strip_tags($new_instance['region']);
        $instance['city'] = strip_tags($new_instance['city']);
        $instance['phone'] = strip_tags($new_instance['phone']);
        $instance['email'] = strip_tags($new_instance['email']);

        return $instance;
    }

    /**
     * Сформировать список временных зон
     *
     * @return string
     * @throws Exception
     */
    private function generate_timezone_list($active_region = '')
    {
        static $allRegions = array(
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC
        );

        // Makes it easier to create option groups next
        $list = array('Africa', 'America', 'Antarctica', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');

        // Make array holding the regions (continents), they are arrays w/ all their cities
        $region = array();
        foreach ($allRegions as $area) {
            array_push($region, DateTimeZone::listIdentifiers($area));
        }

        $count = count($region);
        $i = 0;
        $holder = '';

        // Go through each region one by one, sorting and formatting it's cities
        while ($i < $count) {
            $chunck = $region[$i];

            // Create the region (continents) option group
            $holder .= '<optgroup label="' . $list[$i] . '">';
            $timezone_offsets = array();
            foreach ($chunck as $timezone) {
                $tz = new DateTimeZone($timezone);
                $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
            }
            asort($timezone_offsets);
            $timezone_list = array();
            foreach ($timezone_offsets as $timezone => $offset) {
                $offset_prefix = $offset < 0 ? '-' : '+';
                $offset_formatted = gmdate('H:i', abs($offset));
                $pretty_offset = "UTC ${offset_prefix}${offset_formatted}";
                $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
            }

            // All the formatting is done, finish and move on to next region
            foreach ($timezone_list as $key => $val) {
                $selected = ($active_region == $key) ? ' selected' : ' ';
                $holder .= '<option' . $selected . ' value="' . $key . '">' . $val . '</option>' . PHP_EOL;
            }
            $holder .= '</optgroup>';
            ++$i;
        }
        return $holder;
    }

    /**
     * Получить смещение временной зоны
     *
     * @param $timezone
     * @return string
     * @throws Exception
     */
    private function getTimeOffset($timezone)
    {
        $tz = new DateTimeZone($timezone);
        $dateTimeJapan = new DateTime("now", $tz);
        $timeOffset = $tz->getOffset($dateTimeJapan);
        $offset_prefix = $timeOffset < 0 ? '-' : '+';
        $offset_formatted = gmdate('H:i', abs($timeOffset));

        return $offset_prefix . $offset_formatted;
    }

}

/**
 * Регистрация и активация виджета.
 */
add_action('widgets_init', 'clock_register_widget');
function clock_register_widget()
{
    register_widget('Clock_info_widget');
}
