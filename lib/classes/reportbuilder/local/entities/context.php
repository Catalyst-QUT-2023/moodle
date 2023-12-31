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

declare(strict_types=1);

namespace core\reportbuilder\local\entities;

use core\context_helper;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\report\{column, filter};
use html_writer;
use lang_string;
use stdClass;

/**
 * Context entity
 *
 * @package     core
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return ['context' => 'ctx'];
    }

    /**
     * The default title for this entity in the list of columns/conditions/filters in the report builder
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('context');
    }

    /**
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {
        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        // All the filters defined by the entity can also be used as conditions.
        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this
                ->add_filter($filter)
                ->add_condition($filter);
        }

        return $this;
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        $contextalias = $this->get_table_alias('context');

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('contextname'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            // Sorting may not order alphabetically, but will at least group contexts together.
            ->set_is_sortable(true)
            ->add_callback(static function($contextid, stdClass $context): string {
                if ($contextid === null) {
                    return '';
                }

                context_helper::preload_from_record($context);
                return context_helper::instance_by_id($contextid)->get_context_name();
            });

        // Link.
        $columns[] = (new column(
            'link',
            new lang_string('contexturl'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            // Sorting may not order alphabetically, but will at least group contexts together.
            ->set_is_sortable(true)
            ->add_callback(static function($contextid, stdClass $context): string {
                if ($contextid === null) {
                    return '';
                }

                context_helper::preload_from_record($context);
                $context = context_helper::instance_by_id($contextid);

                return html_writer::link($context->get_url(), $context->get_context_name());
            });

        // Level.
        $columns[] = (new column(
            'level',
            new lang_string('contextlevel'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_fields("{$contextalias}.contextlevel")
            ->set_is_sortable(true)
            // It doesn't make sense to offer integer aggregation methods for this column.
            ->set_disabled_aggregation(['avg', 'max', 'min', 'sum'])
            ->add_callback(static function(?int $level): string {
                if ($level === null) {
                    return '';
                }

                return context_helper::get_level_name($level);
            });

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $contextalias = $this->get_table_alias('context');

        // Level.
        $filters[] = (new filter(
            select::class,
            'level',
            new lang_string('contextlevel'),
            $this->get_entity_name(),
            "{$contextalias}.contextlevel"
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function(): array {
                $levels = context_helper::get_all_levels();

                return array_map(static function(string $levelclass): string {
                    return $levelclass::get_level_name();
                }, $levels);
            });

        return $filters;
    }
}
