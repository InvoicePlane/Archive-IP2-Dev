<?php
/**
 * @package Helpers
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Check if a model has pages
 * @param $model
 * @return bool
 */
function has_pages($model) {
    $CI = &get_instance();

    if ($CI->$model->previous_offset >= 0 && $CI->$model->next_offset <= $CI->$model->last_offset) {
        return true;
    }

    return false;
}

/**
 * Custom pager function
 * @param $base_url
 * @param $model
 * @return string
 */
function pager($base_url, $model)
{
    $CI = &get_instance();

    $pager = '<div class="btn-group btn-group-sm">';

    $pager .= $CI->$model->previous_offset;
    if (($previous_page = $CI->$model->previous_offset) >= 0) {
        $pager .= '<a class="btn btn-default" href="' . $base_url . '/0" title="' . lang('first') . '">';
        $pager .= '<i class="fa fa-angle-double-left no-margin"></i></a>';
        $pager .= '<a class="btn btn-default" href="' . $base_url . '/' . $CI->$model->previous_offset . '" title="' . lang('prev') . '">';
        $pager .= '<i class="fa fa-angle-left no-margin"></i></a>';
    }

    if (($next_page = $CI->$model->next_offset) <= $CI->$model->last_offset) {
        $pager .= '<a class="btn btn-default" href="' . $base_url . '/' . $CI->$model->next_offset . '" title="' . lang('next') . '">';
        $pager .= '<i class="fa fa-angle-right no-margin"></i></a>';
        $pager .= '<a class="btn btn-default" href="' . $base_url . '/' . $CI->$model->last_offset . '" title="' . lang('last') . '">';
        $pager .= '<i class="fa fa-angle-double-right no-margin"></i></a>';
    }

    $pager .= '</div>';

    return $pager;

}
