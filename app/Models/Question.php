<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // لاستخدام العلاقة

class Question extends Model
{
    use HasFactory;

    /**
     * الحقول التي يمكن تعبئتها بشكل جماعي (Mass Assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'text',
        // أضف أي أعمدة أخرى أضفتها في المايجريشن وقابلة للتعبئة
        // 'identifier',
        // 'order',
        // 'is_active',
    ];

    /**
     * تعريف العلاقة: السؤال الواحد له عدة خيارات.
     */
    public function options(): HasMany
    {
        // افترض أن الموديل الآخر هو QuestionOption وأن المفتاح الأجنبي هو question_id
        return $this->hasMany(QuestionOption::class, 'question_id', 'id');
        // يمكنك إضافة ->orderBy('order') هنا لترتيب الخيارات تلقائياً
    }
}