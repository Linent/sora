<?php
$name_html         = !empty( $id ) ? " name='{$id}'" : '';
$name_html         = !empty( $name ) ? " name='{$name}'" : $name_html;
$id_html           = !empty( $id ) ? " id='{$id}'" : '';
$class_html        = !empty( $class ) ? " class='yith-wcbk-month-picker-wrapper {$class}'" : 'yith-wcbk-month-picker-wrapper';
$value_class_html  = !empty( $value_class ) ? " class='yith-wcbk-month-picker-value {$value_class}'" : "class='yith-wcbk-month-picker-value'";
$custom_attributes = ' ' . $custom_attributes;
$data_html         = '';
foreach ( $data as $data_key => $data_value ) {
    $data_html .= " data-{$data_key}='{$data_value}'";
}
/**
 * @var array $not_available_months
 * @var int   $min_date
 * @var int   $max_date
 */
$default_options = array(
    'not_available_months' => array(),
    'min_date'             => time(),
    'max_date'             => strtotime( '+2 years' ),
);

$options = isset( $options ) ? $options : array();
$options = wp_parse_args( $options, $default_options );
extract( $options );

$start_year  = date( 'Y', $min_date );
$start_month = date( 'n', $min_date );

$end_year  = date( 'Y', $max_date );
$end_month = date( 'n', $max_date );

$date_helper = YITH_WCBK_Date_Helper();
?>

<div <?php echo $id_html . $class_html . $custom_attributes . $data_html; ?> data-current-year="<?php echo $start_year; ?>">
    <input type="hidden" <?php echo $value_class_html . $name_html; ?> value="<?php echo $value; ?>">
    <?php
    $current_year = $start_year;
    while ( $current_year <= $end_year ) {
        $display  = $current_year == $start_year ? '' : 'style="display:none"';
        $has_next = $current_year < $end_year;
        $has_prev = $start_year < $current_year;
        ?>
        <div class="year year-<?php echo $current_year; ?>" data-year="<?php echo $current_year; ?>" <?php echo $display; ?>>
            <div class="top-actions">
                <div class="prev <?php echo $has_prev ? 'enabled' : ''; ?>"><span class="dashicons dashicons-arrow-left"></span></div>
                <div class="next <?php echo $has_next ? 'enabled' : ''; ?>"><span class="dashicons dashicons-arrow-right"></span></div>
            </div>
            <table>
                <thead>
                <tr>
                    <th colspan="3"><?php echo $current_year ?> </th>
                </tr>
                </thead>
                <?php
                $months = range( 1, 12 );
                foreach ( $months as $month ) {
                    $month_txt = $month < 10 ? '0' . $month : $month;

                    $enabled       = !in_array( $current_year . '-' . $month_txt, $not_available_months ) && strtotime( $current_year . '-' . $month_txt . '-01' ) > $min_date && strtotime( $current_year . '-' . $month_txt . '-01' ) < $max_date;
                    $enabled_class = $enabled ? 'enabled' : 'disabled';
                    $month_name    = date_i18n( "M", mktime( 0, 0, 0, $month ) );
                    $this_value    = $current_year . '-' . $month_txt . '-01';
                    if ( in_array( $month, array( 1, 4, 7, 10 ) ) )
                        echo '<tr>';
                    echo "<td class='month $enabled_class' data-value='$this_value'>$month_name</td>";
                    if ( in_array( $month, array( 3, 6, 9, 12 ) ) )
                        echo '</tr>';
                }
                ?>
            </table>
        </div>
        <?php
        $current_year++;
    }
    ?>
</div>