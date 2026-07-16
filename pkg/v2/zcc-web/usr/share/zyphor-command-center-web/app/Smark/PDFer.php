<?php

namespace App\Smark;

/**
 * export($data)
 */

use PDF;
use App\Models\User;
use App\Models\Pastconnections;
use App\DataFormatter;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Helpers\Helpers;


class PDFer
{
    // Method to export data as a PDF
    public static function export($data)
    {
        // Include the PDF class
        require 'PDF.php';

        // Create a new PDF document instance
        // Parameters:
        // "L": Page orientation (Landscape)
        // PDF_UNIT: Unit of measure (default)
        // PDF_PAGE_FORMAT: Page format (default)
        // true: Unicode support
        // 'UTF-8': Character encoding
        // false: Disable automatic page breaking
        $pdf = new PDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Sample data array (overwrites the input data)
        $data = $data;

        // Set document information
        $pdf->SetCreator(PDF_CREATOR); // Set the creator of the document
        $pdf->SetAuthor('Your Name'); // Set the author of the document
        $pdf->SetTitle('Service Report'); // Set the title of the document
        $pdf->SetSubject('Using TCPDF for PDF generation'); // Set the subject of the document
        $pdf->SetKeywords('TCPDF, PDF, example, PHP, export'); // Set keywords for the document

        // Set default header data
        $pdf->SetHeaderData('', 0, 'Service Report', ''); // Set header data (left, center, right content)

        // Set header and footer fonts
        $pdf->setHeaderFont(Array('helvetica', '', 10)); // Set font for the header (Helvetica, normal, 10 pt)
        $pdf->setFooterFont(Array('helvetica', '', 8)); // Set font for the footer (Helvetica, normal, 8 pt)

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT); // Set left, top, and right margins
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER); // Set header margin
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER); // Set footer margin

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); // Enable auto page breaks and set bottom margin

        // Add a page to the PDF
        $pdf->AddPage();

        // Custom HTML header
        $html_header = '
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: left; width: 33.33%;">Company Name</td>
                    <td style="text-align: center; width: 33.33%;"><h2>Report Title</h2></td>
                    <td style="text-align: right; width: 33.33%;">Date: ' . date('Y-m-d') . '</td>
                </tr>
            </table>
            <hr>
        ';

        // Output the custom HTML header
        $pdf->writeHTML($html_header, true, false, true, false, '');

        // HTML content for the table with data
        $html_table = '
            <h1>User Data</h1>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Email</th>
                </tr>
        ';

        // Loop through the data and add rows to the HTML table
        foreach ($data as $row) {
            $html_table .= '<tr>';
            $html_table .= '<td>' . $row['ID'] . '</td>'; // Add ID
            $html_table .= '<td>' . $row['Name'] . '</td>'; // Add Name
            $html_table .= '<td>' . $row['Email'] . '</td>'; // Add Email
            $html_table .= '<td>' . $row['Email'] . '</td>'; // Add Email again (duplicate)
            $html_table .= '</tr>';
        }

        $html_table .= '</table>';

        // Output the HTML content - Table with data
        $pdf->writeHTML($html_table, true, false, true, false, '');

        // Close and output the PDF document
        // Parameters:
        // 'example.pdf': Name of the output file
        // 'I': Output destination (I = inline in browser)
        $pdf->Output('example.pdf', 'I');
    }

    public static function exportGeneralDataIntoPDF(
        $graphData,
        $deviceDownloadUpload,
        $totalUsers,
        $totalUniqueUsers,
        $totalCumulativeTraffic,
        $project,
        $supplier,
        $startDate,
        $endDate,
        $acceptanceDate,
        $downloadDate,
        $siteName,
        $siteCode,
        $percentageAvailability,
        $percentageAvailabilityStatus,
        $people
    )
    {
        // Include the PDF class
        require 'PDF.php';

        // Create a new PDF document instance
        // Parameters:
        // "L": Page orientation (Landscape)
        // PDF_UNIT: Unit of measure (default)
        // PDF_PAGE_FORMAT: Page format (default)
        // true: Unicode support
        // 'UTF-8': Character encoding
        // false: Disable automatic page breaking
        $pdf = new PDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Sample data array (overwrites the input data)
        // $data = $data;

        // Set document information
        $pdf->SetCreator(PDF_CREATOR); // Set the creator of the document
        $pdf->SetAuthor('Your Name'); // Set the author of the document
        $pdf->SetTitle('Service Report'); // Set the title of the document
        $pdf->SetSubject('Using TCPDF for PDF generation'); // Set the subject of the document
        $pdf->SetKeywords('TCPDF, PDF, example, PHP, export'); // Set keywords for the document

        // Set default header data
        $pdf->SetHeaderData('', 0, 'Service Report', ''); // Set header data (left, center, right content)

        // Set header and footer fonts
        $pdf->setHeaderFont(Array('helvetica', '', 10)); // Set font for the header (Helvetica, normal, 10 pt)
        $pdf->setFooterFont(Array('helvetica', '', 8)); // Set font for the footer (Helvetica, normal, 8 pt)

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT); // Set left, top, and right margins
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER); // Set header margin
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER); // Set footer margin

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); // Enable auto page breaks and set bottom margin

        // FIRST PAGE +=======================================================================================

        // Add a page to the PDF
        $pdf->AddPage();

        // Custom HTML header
        $html_header = '

            <table style="width: 100%;">
                <tr>
                    <td style="width: 20%;"></td>
                    <td style="width: 60%; text-align: center;">
                        <div style="margin-top: 20px;">
                            <img src="assets/dict-logo-sr.jpg" height="90">
                        </div>
                    </td>
                    <td style="width: 20%;"></td>
                </tr>
            </table>

            <table style="width: 100%;">
                <tr>
                    <td style="width: 20%;"></td>
                    <td style="width: 60%;"></td>
                    <td style="width: 20%;"><i><b>Annex B</b></i></td>
                </tr>
                <tr>
                    <td style="width: 20%;"></td>
                    <td style="width: 60%; text-align: center;">
                        <h1>SERVICE REPORT</h1> <br>
                    </td>
                    <td style="width: 20%;"></td>
                </tr>
                <tr>
                    <td style="width: 20%;">
                            <b style="font-size: 10px;">PROJECT</b>
                    </td>
                    <td style="width: 80%; text-align: left;">
                            <b style="font-size: 10px;">: '. $project .'</b>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%;">
                            <b style="font-size: 10px;">SUPPLIER</b>
                    </td>
                    <td style="width: 80%; text-align: left;">
                            <b style="font-size: 10px;">: '. $supplier .'</b>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%;">
                            <b style="font-size: 10px;">PERIOD</b>
                    </td>
                    <td style="width: 80%; text-align: left;">
                            <b style="font-size: 10px;">: ' . $startDate . ' to ' . $endDate . '</b>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%;"></td>
                    <td style="width: 80%; text-align: left;">
                            <b style="font-size: 10px;">I. SERVICE REPORT AS PER SERVICE PROVIDER’S NETWORK MONITORING SYSTEM:</b> <br>
                            <b style="font-size: 10px;">• ACCEPTANCE DATE:  '. $acceptanceDate .'</b> <br>
                            <b style="font-size: 10px;">• DATE OF DOWNLOAD:  '. $downloadDate .'</b> <br>
                            <b style="font-size: 10px;">• NETWORK AVAILABILITY:</b> <br>
                    </td>
                </tr>
            </table>
            ';

        // Output the custom HTML header
        $pdf->writeHTML($html_header, true, false, true, false, '');

        $html_table_network_availability = '
            <table style="width: 100%;" border="1" cellpadding="2" cellspacing="0">
                <tr style="background-color: #B8CCE4; color: #000; text-align: center;">
                    <th><b>SITE NAME</b></th>
                    <th><b>SITE ID / Service ID</b></th>
                    <th><b>SERVICE AVAILABILITY</b></th>
                </tr>

                <tr style="background-color: #ffffff; color: #000; text-align: center;">
                    <td>'.$siteName.'</td>
                    <td>'.$siteCode.'</td>
                    <td>'.$percentageAvailabilityStatus.'</td>
                </tr>
        ';

        // Loop through the provided data and generate content

        $html_table_network_availability .= '</table>';
        $html_table_network_availability .= '<p>
            <b><i>Passed*:</i></b> passed with justification(s) attached. <br>
            <b><i>Passed:</i></b> passed w/out justification(s) attached; complied beyond the required percentage of service availability. <br>
            <b><i>Failed:</i></b> fail
        </p>';
        $html_table_network_availability .= '<b><i>*(Kindly attached equipment logs on the outages)</i></b> <br> <br>';


        $pdf->writeHTML($html_table_network_availability, true, false, true, false, '');

        $html_assignatories = '';

        // Group the people into rows of 2 per table row
        $chunks = array_chunk($people, 2);

        // Loop through each group to build the table
        foreach ($chunks as $chunk) {

            $html_assignatories .= '<table cellpadding="2" cellspacing="0">';

            // Table headers (purpose)
            $html_assignatories .= '<tr style="text-align: center; font-size: 10px;">';
            foreach ($chunk as $person) {
                $html_assignatories .= '<th>' . htmlspecialchars($person['purpose']) . ':</th>';
            }
            $html_assignatories .= '</tr>';

            // Table body (name and designation)
            $html_assignatories .= '<tr style="text-align: center; font-size: 10px;">';
            foreach ($chunk as $person) {
                $html_assignatories .= '<td>
                    <u style="font-weight: bold;">' . htmlspecialchars($person['name']) . '</u><br>
                    ' . htmlspecialchars($person['designation']) . '
                </td>';
            }
            $html_assignatories .= '</tr>';

            $html_assignatories .= '</table><br><br>';
        }

        // Output into PDF
        $pdf->writeHTML($html_assignatories, true, false, true, false, '');

        // Add a page to the PDF
        $pdf->AddPage();

        $html_percentage_availability = '

            <table style="width: 100%;">
                <tr>
                    <td style="width: 20%;"></td>
                    <td style="width: 60%; text-align: center;">
                        <div style="margin-top: 20px;">
                            <img src="assets/dict-logo-sr.jpg" height="90">
                        </div>
                    </td>
                    <td style="width: 20%;"></td>
                </tr>
            </table>

            <div style="text-align: center;">
                <b>Percentage Availability</b> <br>
            </div>

            <table border="1" cellpadding="2" cellspacing="0">
                <tr style="background-color: #B8CCE4; color: #000; text-align: center;">
                    <th><b>Site Name</b></th>
                    <th><b>Percentage Availability</b></th>
                </tr>
                <tr style="background-color: #ffffff; color: #000; text-align: center;">
                    <td>'.$siteName.'</td>
                    <td>'.$percentageAvailability.'</td>
                </tr>
            </table>';

        $html_percentage_availability .= '<br><br>
            <b style="text-align: center;">Traffic Activities</b> <br>
            ' . $startDate . ' to ' . $endDate . '
        ';

        $pdf->writeHTML($html_percentage_availability, true, false, true, false, '');


        // Add a page to the PDF
        $pdf->AddPage();

        $html_access_point_name_and_model = '

            <div style="text-align: center;">
                <b>Access Point Names and Model</b> <br>
                <b style="color: red; font-style: italic;">'.$siteName.'</b> <br>
                ' . $startDate . ' to ' . $endDate . '
            </div>

            <table border="1" cellpadding="2" cellspacing="0">
                <tr style="background-color: #B8CCE4; color: #000; text-align: center;">
                    <th><b>Location Name</b></th>
                    <th><b>MAC Address</b></th>
                    <th><b>Model</b></th>
                    <th><b>Cumulative Traffic</b></th>
                </tr>';

        foreach($deviceDownloadUpload as $key => $value) {
            $html_access_point_name_and_model .= '
                <tr style="background-color: #FFFFFF; color: #000; text-align: center;">
                    <td>'.$value['location_name'].'</td>
                    <td>'.$value['mac'].'</td>
                    <td>'.$value['model'].'</td>
                    <td>'.$value['cumulative_traffic'].'</td>
                </tr>
            ';
        }

        $html_access_point_name_and_model .= '</table> <br><br>';

        $pdf->writeHTML($html_access_point_name_and_model, true, false, true, false, '');

        $html_users_and_unique_users = '
            <div style="text-align: center;">
                <b>Users and Unique Users</b> <br>
                ' . $startDate . ' to ' . $endDate . '
            </div>

            <table border="1" cellpadding="4" cellspacing="0" width="100%">
                <tr style="background-color: #B8CCE4; color: #000; text-align: center;">
                    <th><b>Site Name</b></th>
                    <th><b>Total Users</b></th>
                    <th><b>Unique Users</b></th>
                    <th><b>Total Cumulative Traffic</b></th>
                </tr>
                <tr style="background-color: #FFFFFF; color: #000; text-align: center;">
                    <td>'.$siteName.'</td>
                    <td>'.$totalUsers.'</td>
                    <td>'.$totalUniqueUsers.'</td>
                    <td>'.Helpers::bytesToHuman($totalCumulativeTraffic).'</td>
                </tr>
            </table>';

        $pdf->writeHTML($html_users_and_unique_users, true, false, true, false, '');


// =================================================================================================================

        $pdf->setPage(2);

        // Title
        $pdf->SetFont('helvetica', 'B', 12);

        // ================== PREPARE DATA ==================
        $data = $graphData;

        $activities = $data;

        // Sort by date ascending
        usort($activities, fn($a, $b) => $a['date'] <=> $b['date']);

        // ✅ Index activities by date label (zero-padded)
        $txByDate = [];
        $dxByDate = [];

        foreach ($activities as $row) {
            $label = date('M d', $row['date']); // "Mar 03"
            $txByDate[$label] = ($txByDate[$label] ?? 0) + $row['txData'];
            $dxByDate[$label] = ($dxByDate[$label] ?? 0) + $row['dxData'];
        }

        // ✅ Build full date range (zero-padded, same format)
        $dataTx = [];
        $dataDx = [];

        $cleanStartDate = preg_replace('/\s*\(.*?\)/', '', $startDate);
        $cleanEndDate = preg_replace('/\s*\(.*?\)/', '', $endDate);

        $current = strtotime($cleanStartDate);
        $end = strtotime($cleanEndDate);

        while ($current <= $end) {
            $label = date('M d', $current); // "Mar 03"
            $dataTx[$label] = $txByDate[$label] ?? 0;
            $dataDx[$label] = $dxByDate[$label] ?? 0;
            $current = strtotime('+1 day', $current);
        }

        // ==================================================
        // GRAPH CONFIGURATION
        // ==================================================
        $graphHeight = 40;
        $graphWidth  = 150;

        $x = 45;     // left margin
        $y = 170;    // baseline (zero line)

        // ==================================================
        // DATA PREPARATION
        // ==================================================
        $dataAll = array_merge(
            array_values($dataTx),
            array_values($dataDx)
        );

        $pointCount = count($dataTx);
        $pointGap   = $graphWidth / max(1, $pointCount - 1);

        $maxValue = max($dataAll);
        $minValue = min($dataAll);

        // Detect if real zero exists
        $hasZero = in_array(0, $dataAll, true);

        // ==================================================
        // SCALE LOGIC
        // ==================================================
        $allNonZero = array_filter($dataAll, function($v) { return $v != 0; });

        if (!empty($allNonZero)) {
            $avgNonZero = array_sum($allNonZero) / count($allNonZero);
            $minNonZero = min($allNonZero);

            // Convert 20 GB threshold to bytes
            $thresholdBytes = 20 * 1024 * 1024 * 1024; // 20 GB
            $offsetBytes = 100 * 1024 * 1024 * 1024;   // 100 GB visual offset

            // If min non-zero is below threshold, adjust scale to add visual offset
            if ($minNonZero > 0 && $minNonZero < $thresholdBytes) {
                // Add offset below minimum to create visual separation
                $scaleMin = max(0, $minNonZero - $offsetBytes);
                $scaleMax = $maxValue + ($maxValue * 0.1);
            } else {
                // Normal distribution - use standard padding
                $padding = ($maxValue - $minNonZero) * 0.10;
                $scaleMin = max(0, $minNonZero - $padding);
                $scaleMax = $maxValue + $padding;
            }
        } else {
            // All zeros
            $scaleMin = 0;
            $scaleMax = 1;
        }

        $range = max(1, $scaleMax - $scaleMin);

        // ==================================================
        // AXES
        // ==================================================
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.5);

        // X-axis (baseline)
        $pdf->Line($x - 10, $y, $x + $graphWidth, $y);

        // Y-axis
        $pdf->Line($x - 10, $y, $x - 10, $y - $graphHeight - 10);

        // ==================================================
        // Y-AXIS LABELS + HORIZONTAL GRID
        // ==================================================
        $pdf->SetFont('helvetica', '', 7);
        $steps = 5;

        for ($i = 0; $i <= $steps; $i++) {
            $value = $scaleMin + ($range / $steps) * $i;
            $posY  = $y - ((($value - $scaleMin) / $range) * $graphHeight);

            // Bytes → GB
            $label = number_format($value / (1024 * 1024 * 1024), 2) . ' GB';

            $pdf->SetTextColor(0, 0, 0);
            $pdf->Text($x - 30, $posY - 2, $label);

            // Horizontal grid line
            $pdf->SetDrawColor(220, 220, 220);
            $pdf->SetLineWidth(0.3);
            $pdf->Line($x - 10, $posY, $x + $graphWidth, $posY);
        }

        // ==================================================
        // COLORS
        // ==================================================
        $colors = [
            'tx' => [70, 90, 130],     // dark teal
            'dx' => [0, 150, 120],     // dark blue-gray
        ];

        // ==================================================
        // SMOOTH LINE DRAWER WITH LINE BREAKS AT ZERO
        // ==================================================
        function drawSmoothLine(
            $pdf,
            $data,
            $otherData,
            $x,
            $y,
            $pointGap,
            $scaleMin,
            $range,
            $graphHeight,
            $color,
            $drawXLabels = false,
            $labelEvery = 3
        ) {
            $pdf->SetFont('helvetica', '', 7);

            $points = [];
            $index  = 0;

            // Build all points
            foreach ($data as $label => $value) {
                $px = $x + ($index * $pointGap);
                $py = $y - ((($value - $scaleMin) / $range) * $graphHeight);

                $points[] = [
                    'x'     => $px,
                    'y'     => $py,
                    'value' => $value,
                    'label' => $label,
                    'i'     => $index
                ];

                $index++;
            }

            // Draw vertical grid lines for all points
            $pdf->SetDrawColor(235, 235, 235);
            $pdf->SetLineWidth(0.3);

            foreach ($points as $p) {
                $pdf->Line($p['x'], $y, $p['x'], $y - $graphHeight);
            }

            // Draw smooth curves, breaking at zero values
            $pdf->SetDrawColor($color[0], $color[1], $color[2]);
            $pdf->SetLineWidth(0.8);

            $previousPoint = null;

            foreach ($points as $currentPoint) {
                // If current value is zero, break the line
                if ($currentPoint['value'] == 0) {
                    $previousPoint = null;
                    continue;
                }

                // Draw curve from previous point to current point
                if ($previousPoint !== null) {
                    $controlX = ($previousPoint['x'] + $currentPoint['x']) / 2;

                    $pdf->Curve(
                        $previousPoint['x'], $previousPoint['y'],
                        $controlX, $previousPoint['y'],
                        $controlX, $currentPoint['y'],
                        $currentPoint['x'], $currentPoint['y']
                    );
                }

                $previousPoint = $currentPoint;
            }

            // Draw X-axis labels
            if ($drawXLabels) {
                foreach ($points as $p) {
                    if ($p['i'] % $labelEvery === 0) {
                        $label = $p['label'];

                        // Always show label at regular intervals
                        $pdf->Text($p['x'] - 5, $y + 4, $label);
                    }
                }
            }
        }

        // Add this function after drawSmoothLine
        function drawDataPoints(
            $pdf,
            $data,
            $x,
            $y,
            $pointGap,
            $scaleMin,
            $range,
            $graphHeight,
            $color
        ) {
            $index = 0; // Add numeric counter

            foreach ($data as $label => $value) {
                if ($value == 0) {
                    $index++;
                    continue; // Skip zero values but still increment index
                }

                $px = $x + ($index * $pointGap);
                $py = $y - ((($value - $scaleMin) / $range) * $graphHeight);

                // Draw a small circle/dot
                $pdf->SetFillColor($color[0], $color[1], $color[2]);
                $pdf->Circle($px, $py, 0.75, 0, 360, 'F');

                $index++;
            }
        }

        // ==================================================
        // DRAW BOTH GRAPH LINES
        // ==================================================
        drawSmoothLine(
            $pdf,
            $dataTx,
            $dataDx,
            $x,
            $y,
            $pointGap,
            $scaleMin,
            $range,
            $graphHeight,
            $colors['tx'],
            true,
            3
        );

        drawSmoothLine(
            $pdf,
            $dataDx,
            $dataTx,
            $x,
            $y,
            $pointGap,
            $scaleMin,
            $range,
            $graphHeight,
            $colors['dx']
        );

        // After the two drawSmoothLine calls, add:
        drawDataPoints($pdf, $dataTx, $x, $y, $pointGap, $scaleMin, $range, $graphHeight, $colors['tx']);
        drawDataPoints($pdf, $dataDx, $x, $y, $pointGap, $scaleMin, $range, $graphHeight, $colors['dx']);

        // Output the final PDF
        $pdf->Output("SERVICE_REPORT".'.pdf', 'I');
    }
}
