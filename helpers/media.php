<?php
/**
 * 
 * @param int $orig_w
 * @param int $orig_h
 * @return type
 */
function image_resize_dimensions_no_crop($orig_w, $orig_h) {
	return array($orig_w, $orig_h, 0, 0, $orig_w, $orig_h, $orig_w, $orig_h);
}
