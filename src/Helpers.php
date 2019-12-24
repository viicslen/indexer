<?php

if (!function_exists('indexerOptimizedKey')) {
    function indexerOptimizedKey(array $query): string
    {
        return trim($query['explain_result']['key']);
    }
}

if (!function_exists('indexerGetOptimizedCount')) {
    function indexerGetOptimizedCount(array $queries): string
    {
        $optimizationsCount = 0;

        foreach ($queries as $query) {
            if (indexerOptimizedKey($query)) {
                $optimizationsCount++;
            }
        }

        return $optimizationsCount;
    }
}

if (!function_exists('indexerMakeExplainResults')) {
    function indexerMakeExplainResults(array $queries): string
    {
        $output = '';

        foreach ($queries as $query) {

            $bgColor = indexerOptimizedKey($query) ? '#91e27f' : '#dae0e5';

            $output .= '<div class="indexer_section">';
            $output .= '<div class="indexer_section_details" style="background: ' . $bgColor . '">';
            $output .= "<div class='left'><strong>$query[index_name]</strong></div>";
            $output .= "<div class='right'><strong>$query[time]</strong></div>";
            $output .= "<div class='clear'></div>";
            $output .= '</div>';
            $output .= "<div class='padded'>";
            $output .= "File: <strong>$query[file]</strong><br>";
            $output .= "Line: <strong>$query[line]</strong>";
            $output .= '</div>';
            $output .= '<div class="sql">' . $query['sql'] . '</div>';
            $output .= indexerTable([$query['explain_result']]);

            if ($query['hints']) {
                $output .= "<div class='padded'>";

                foreach ($query['hints'] as $hint) {
                    $output .= "<span class='hint'>Hint</span> <strong>$hint</strong><br>";
                }

                $output .= '</div>';
            }

            $output .= '</div>';
        }

        return $output;
    }
}

if (!function_exists('indexerTable')) {
    function indexerTable($array, $table = true): string
    {
        $out = '';

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!isset($tableHeader)) {
                    $tableHeader = '<th>' . implode('</th><th>', array_keys($value)) . '</th>';
                }

                array_keys($value);

                $out .= '<tr>';
                $out .= indexerTable($value, false);
                $out .= '</tr>';
            } else {
                $out .= "<td>$value</td>";
            }
        }

        if ($table) {
            return '<table class="indexer_table">' . $tableHeader . $out . '</table>';
        }

        return $out;
    }
}

