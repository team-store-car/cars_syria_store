<?php

namespace Database\Factories;

use App\Models\Category;

use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       // قوائم واقعية للسيارات
       $brands = [
        'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Prius', 'Highlander'],
        'BMW' => ['X5', '3 Series', '5 Series', 'M3', 'X3'],
        'Mercedes-Benz' => ['C-Class', 'E-Class', 'S-Class', 'GLC', 'GLE'],
        'Ford' => ['F-150', 'Mustang', 'Explorer', 'Focus', 'Escape'],
        'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot', 'Fit'],
        'Tesla' => ['Model 3', 'Model S', 'Model X', 'Model Y'],
        'Hyundai' => ['Tucson', 'Elantra', 'Santa Fe', 'Sonata', 'Kona'],
        'Audi' => ['A3', 'A4', 'Q5', 'Q7', 'TT'],
        'Volkswagen' => ['Golf', 'Passat', 'Tiguan', 'Jetta', 'Atlas'],
    ];

    // اختيار ماركة عشوائية
    $brand = $this->faker->randomElement(array_keys($brands));
    // اختيار موديل مرتبط بالماركة
    $model = $this->faker->randomElement($brands[$brand]);
    // إنشاء اسم السيارة من الماركة والموديل
    $name = "$brand $model";

    // حالة السيارة
    $condition = $this->faker->randomElement(['new', 'used']);
    // المسافة المقطوعة بناءً على الحالة
    $mileage = $condition === 'new' ? 0 : $this->faker->numberBetween(1000, 150000);

    // عدد المقاعد بناءً على نوع السيارة
    $seats = in_array($model, ['RAV4', 'Highlander', 'X5', 'GLC', 'Q7', 'Santa Fe', 'Tiguan', 'Atlas', 'Explorer', 'Pilot', 'Model X'])
        ? $this->faker->numberBetween(5, 7) // SUV
        : $this->faker->numberBetween(2, 5); // سيدان أو رياضية

    // قوة الحصان بناءً على نوع السيارة
    $horsepower = in_array($model, ['Mustang', 'M3', 'TT', 'Model S'])
        ? $this->faker->numberBetween(300, 600) // سيارات رياضية
        : $this->faker->numberBetween(100, 350); // سيارات عادية

    return [
        'name' => $name,
        'user_id' => User::factory()->create()->id, // إنشاء مستخدم جديد أو اختيار موجود
        'brand' => $brand,
        'category_id' => Category::factory()->create()->id, // إنشاء فئة جديدة أو اختيار موجود
        'country_of_manufacture' => $this->faker->randomElement(['Germany', 'Japan', 'USA', 'South Korea', 'China', 'UK', 'France']),
        'model' => $model,
        'year' => $this->faker->numberBetween(2000, date('Y')), // سنوات من 2000 إلى السنة الحالية
        'condition' => $condition,
        'mileage' => $mileage,
        'fuel_type' => $this->faker->randomElement(['Petrol', 'Diesel', 'Electric', 'Hybrid']),
        'transmission' => $this->faker->randomElement(['Automatic', 'Manual']),
        'horsepower' => $horsepower,
        'seats' => $seats,
        'color' => $this->faker->randomElement(['Black', 'White', 'Silver', 'Blue', 'Red', 'Gray', 'Green']),
        'description' => $this->faker->optional(0.7)->paragraphs(1, true), // وصف بجملة واحدة بنسبة 70%
        'is_featured' => $this->faker->boolean(20), // 20% فقط مميز
        'other_benefits' => $this->faker->optional(0.5)->randomElement([
            'Extended warranty included',
            'Free maintenance for 1 year',
            'Premium audio system',
            'Leather seats',
            'Navigation system',
        ]), // فوائد إضافية بنسبة 50%
    ];
}


}
