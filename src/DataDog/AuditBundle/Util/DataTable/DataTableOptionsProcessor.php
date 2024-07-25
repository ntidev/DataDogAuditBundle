<?php

namespace DataDog\AuditBundle\Util\DataTable;

/**
 * Class DataTableOptionsProcessor
 * @package DataDog\AuditBundle\Util\DataTable
 */
class DataTableOptionsProcessor {

    public static function GetOptions($request) {
        $draw = $request->get('draw');
        $search = $request->get('search');
        if(is_array($search) && isset($search["value"])) {
            $search = $search["value"];
        }
        $orderBy = $request->get('order');
        $columns = $request->get('columns');
        $length = $request->get('length') ?? $request->get('limit');
        $start = $request->get('start');

        $filters = array();

        // Prepare the filters
        foreach($request->query->all() as $key => $value) {
            if(FALSE !== strpos($key, "filter_") ){
                $filters[str_replace("filter_", "", $key)] = $value;
            }
        }

        // Manage sort by
        $order = null;
        $sortBy = null;
        if(is_array($orderBy) && count($orderBy) > 0 && isset($orderBy[0]['column'])) {
            if(is_array($columns) && isset($columns[$orderBy[0]['column']])) {
                $sortBy = $columns[$orderBy[0]['column']]['data'];
            }
            if(isset($orderBy[0]['dir'])) {
                $order = $orderBy[0]['dir'];
            }
        }

        return array(
            "draw" => $draw,
            "search" => $search,
            "length" => $length,
            "start" => $start,
            "filters" => $filters,
            "orderBy" => $order,
            "sortBy" => $sortBy,
            "columns" => $columns,
        );
    }
}