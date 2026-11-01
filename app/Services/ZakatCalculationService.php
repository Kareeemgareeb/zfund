<?php

namespace App\Services;

use InvalidArgumentException;

class ZakatCalculationService
{
    public function calculateWealth(float $amount, float $goldPricePerGram, bool $hawlCompleted = false): array
    {
        $nisab = round(85 * $goldPricePerGram, 2);
        $eligible = $amount >= $nisab && $hawlCompleted;
        return $this->result($eligible, $nisab, $amount, $eligible ? round($amount * .025, 2) : 0, 'دينار ليبي',
            'النصاب محسوب على أساس 85 جرام ذهب خالص بسعر أدخلته أنت؛ نسبة الزكاة 2.5% بعد الحول. راجع جهة الفتوى بشأن اعتماد معيار الذهب أو الفضة وضم الأموال والديون.', $hawlCompleted);
    }

    public function calculatePreciousMetal(string $metal, float $grams, float $pricePerGram, bool $hawlCompleted): array
    {
        if (!in_array($metal, ['gold', 'silver'], true)) throw new InvalidArgumentException('نوع المعدن غير معروف');
        $nisab = $metal === 'gold' ? 85.0 : 595.0;
        $eligible = $grams >= $nisab && $hawlCompleted;
        $dueGrams = $eligible ? round($grams * .025, 4) : 0;
        return $this->result($eligible, $nisab, $grams, round($dueGrams * $pricePerGram, 2), 'دينار ليبي',
            'الوزن المدخل يجب أن يكون وزن المعدن الخالص؛ المقدار 2.5% بعد الحول. أحكام حلي الاستعمال والعيارات تحتاج مراجعة شرعية.', $hawlCompleted)
            + ['zakat_due_grams' => $dueGrams, 'unit' => 'جرام'];
    }

    public function calculateCrops(float $kilograms, string $irrigation): array
    {
        $rates = ['rain' => .10, 'cost' => .05, 'mixed' => .075];
        if (!isset($rates[$irrigation])) throw new InvalidArgumentException('طريقة الري غير صحيحة');
        $eligible = $kilograms >= 653;
        return $this->result($eligible, 653, $kilograms, $eligible ? round($kilograms * $rates[$irrigation], 2) : 0,
            'كجم', 'النصاب التقريبي 653 كجم (خمسة أوسق). لا يشترط الحول للزروع؛ تجب عند الحصاد. المختلط 7.5% تقدير لحال تساوي الطريقتين، ويُراجع التفصيل عند اختلافهما.');
    }

    public function calculateLivestock(string $animalType, int $count, bool $hawlCompleted = false, bool $grazing = false): array
    {
        $thresholds = ['sheep' => 40, 'cow' => 30, 'camel' => 5];
        if (!isset($thresholds[$animalType])) throw new InvalidArgumentException('نوع الأنعام غير صحيح');
        $nisab = $thresholds[$animalType];
        $eligible = $count >= $nisab && $hawlCompleted && $grazing;
        $description = 'لا تجب الزكاة بحسب هذا النموذج حتى يتحقق النصاب والحول وكون الأنعام سائمة؛ حالات العلف والتجارة والعمل لها أحكام أخرى.';
        $due = 0;
        $unit = 'رأس';
        if ($eligible) {
            if ($animalType === 'sheep') {
                $due = $count <= 120 ? 1 : ($count <= 200 ? 2 : ($count <= 399 ? 3 : intdiv($count, 100)));
                $unit = 'شاة';
                $description = "الواجب {$due} من الغنم وفق شرائح النصاب.";
            } elseif ($animalType === 'cow') {
                [$tabi, $musinna] = $this->cattleDistribution($count);
                $due = $tabi + $musinna;
                $unit = 'رأس من البقر';
                $description = "الواجب {$tabi} تبيع/تبيعة (سنة) و{$musinna} مسنة (سنتان).";
            } else {
                [$due, $unit, $description] = $this->camelDue($count);
            }
        }
        return $this->result($eligible, $nisab, $count, $due, $unit, $description, $hawlCompleted)
            + ['grazing_confirmed' => $grazing];
    }

    private function cattleDistribution(int $count): array
    {
        // Find a valid combination of 30s and 40s that covers the largest eligible block.
        $best = [0, 0, -1];
        for ($forties = 0; $forties <= intdiv($count, 40); $forties++) {
            $thirties = intdiv($count - 40 * $forties, 30);
            $covered = $thirties * 30 + $forties * 40;
            if ($covered > $best[2]) $best = [$thirties, $forties, $covered];
        }
        return [$best[0], $best[1]];
    }

    private function camelDue(int $count): array
    {
        if ($count <= 24) {
            $sheep = intdiv($count, 5);
            return [$sheep, 'شاة', "الواجب {$sheep} من الغنم."];
        }
        $levels = [35 => 'بنت مخاض', 45 => 'بنت لبون', 60 => 'حقة', 75 => 'جذعة', 90 => 'بنتا لبون', 120 => 'حقتان'];
        foreach ($levels as $max => $description) {
            if ($count <= $max) return [in_array($description, ['بنتا لبون', 'حقتان'], true) ? 2 : 1, 'رأس من الإبل', 'الواجب: ' . $description . '.'];
        }
        // From 121: choose a combination of 40 (bint labun) and 50 (hiqqa).
        $best = [0, 0, -1];
        for ($fifties = 0; $fifties <= intdiv($count, 50); $fifties++) {
            $forties = intdiv($count - 50 * $fifties, 40);
            $covered = 40 * $forties + 50 * $fifties;
            if ($covered > $best[2]) $best = [$forties, $fifties, $covered];
        }
        [$bintLabun, $hiqqa] = $best;
        return [$bintLabun + $hiqqa, 'رأس من الإبل', "الواجب {$bintLabun} بنت لبون و{$hiqqa} حقة (وفق تجزئة الأربعين والخمسين)."];
    }

    private function result(bool $eligible, float $nisab, float $amount, float|int $due, string $unit, string $breakdown, ?bool $hawl = null): array
    {
        return ['is_eligible' => $eligible, 'nisab_threshold' => $nisab, 'amount_evaluated' => $amount,
            'zakat_due' => $due, 'currency' => $unit, 'unit' => $unit, 'breakdown' => $breakdown,
            'hawl_completed' => $hawl];
    }
}
