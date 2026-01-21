<?php

Class Utils{
    // -------- Temps restant avant la fin de l'annonce --------
    public static function get_time_left(string $end_date): array{
        
        $endTimestamp = strtotime($end_date);
        $now = strtotime(AppTime::get_current_datetime());  
        
        $diff = $endTimestamp - $now;

        $days = floor($diff / 86400);
        $hours = floor(($diff % 86400) / 3600);    
        $minutes = floor(($diff % 3600) / 60);     
        $seconds = $diff % 60;                     

        return [$days, $hours, $minutes, $seconds];
    }

    // -------- Formate un prix de façon 00,00 --------
    public static function convert_format_number_for_bids(?float $amount) : string{
        if ($amount === null) {
            return " ";  
        }

        $amount = (float) $amount;
        return ("€ ".number_format($amount,2,',','.'));
    }
}