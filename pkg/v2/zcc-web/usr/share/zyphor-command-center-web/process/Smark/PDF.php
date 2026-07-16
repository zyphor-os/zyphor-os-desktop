<?php

// Extend TCPDF class to create custom header and footer
class PDF extends TCPDF {
    public function Header() {
        $this->SetFont('helvetica', 'B', 12);
        $this->Ln(10); // Space from top of content

        // Draw border
        $pageWidth = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();
        $margins = $this->getMargins();

        $marginLeft = $margins['left'];
        $marginTop = $margins['top'];
        $marginRight = $margins['right'];
        $marginBottom = $margins['bottom'];

        $this->SetLineWidth(0.8);
        $rectX = $marginLeft - 5; // move outside the content padding
        $rectY = $marginTop - 5;
        $rectWidth = $pageWidth - $marginLeft - $marginRight + 10;
        $rectHeight = $pageHeight - $marginTop - $marginBottom + 10;

        $this->Rect($rectX, $rectY, $rectWidth, $rectHeight);
    }

    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(110, 133, 161);

        $this->Cell(
            0, 10,
            'DICT Regional Office VIII, Brgy. 1 & 4 A. Mabini St., Port Area, Tacloban City | region8@dict.gov.ph, Telephone Number ((053) 832-4127)',
            'T', 1, 'C'
        );

        $this->SetTextColor(0, 0, 0);
        $this->Cell(
            0, 10,
            'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(),
            0, false, 'C'
        );
    }
}
