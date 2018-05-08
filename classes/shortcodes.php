<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Shortcodes handler.
 *
 * @package    block_stash
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

use block_stash\drop;
use block_stash\item;
use block_stash\manager;
use block_stash\output\drop as droprenderable;
use block_stash\output\drop_image;
use block_stash\output\drop_text;
use block_stash\output\trade as traderenderable;

/**
 * Shortcodes handler class.
 *
 * @package    block_stash
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shortcodes {

    /**
     * Handle the shortcode.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment.
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
    public static function drop($tag, $args, $content, $env, $next) {
        global $PAGE;

        $text = isset($args['text']) ? $args['text'] : '';
        $image = !empty($args['image']) || empty($text);
        $hash = isset($args['secret']) ? $args['secret'] : null;

        if (empty($hash) || strlen($hash) < 6) {
            return '';
        }

        $context = $env->context->get_course_context(false);
        if (!$context) {
            return '';
        }

        $manager = manager::get($context->instanceid);
        if (!$manager->is_enabled()) {
            return '';
        }

        // Attempt to find the drop.
        try {
            $drop = $manager->get_drop_by_hashcode_portion($hash);
            $item = $manager->get_item($drop->get_itemid());

        } catch (dml_exception $e) {
            // Most likely the drop doesn't exist, or the hash is ambiguous.
            return '';

        } catch (coding_exception $e) {
            // Some error occured, who knows?
            return '';
        }

        // Can they see the drop?
        if (!$manager->is_drop_visible($drop)) {
            return '';
        }

        $output = $PAGE->get_renderer('block_stash');
        $renderable = new droprenderable($drop, $item, $manager);
        if (!$image) {
            $renderable = new drop_text($renderable, $text, []);
        } else {
            $renderable = new drop_image($renderable, $text, []);
        }

        return $output->render($renderable);
    }

    /**
     * Handle the shortcode.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment.
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
    public static function trade($tag, $args, $content, $env, $next) {
        global $PAGE;

        $hash = isset($args['secret']) ? $args['secret'] : null;
        if (empty($hash) || strlen($hash) < 6) {
            return '';
        }

        $context = $env->context->get_course_context(false);
        if (!$context) {
            return '';
        }

        $manager = manager::get($context->instanceid);
        if (!$manager->is_enabled()) {
            return '';
        }

        // Attempt to find the trade.
        try {
            $trade = $manager->get_trade_by_hashcode_portion($hash);

        } catch (dml_exception $e) {
            // Most likely the drop doesn't exist, or the hash is ambiguous.
            return '';

        } catch (coding_exception $e) {
            // Some error occured, who knows?
            return '';
        }

        $tradeitems = $manager->get_trade_items($trade->get_id());
        $renderable = new traderenderable($trade, $manager, $tradeitems);
        $output = $PAGE->get_renderer('block_stash');
        return $output->render($renderable);
    }

}

