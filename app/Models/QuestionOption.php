<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // لاستخدام العلاقة

class QuestionOption extends Model
{
    use HasFactory;

    /**
     * اسم الجدول المرتبط بالموديل (إذا كان مختلفاً عن الجمع التلقائي).
     * عادةً لا تحتاج لهذا السطر إذا كان اسم الجدول هو 'question_options'.
     * @var string
     */
    // protected $table = 'question_options';

    /**
     * الحقول التي يمكن تعبئتها بشكل جماعي.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question_id',
        'text',
        // أضف أي أعمدة أخرى أضفتها في المايجريشن وقابلة للتعبئة
        // 'value',
        // 'order',
        // 'is_active',
    ];

    /**
     * تعريف العلاقة: الخيار الواحد ينتمي لسؤال واحد.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }
}