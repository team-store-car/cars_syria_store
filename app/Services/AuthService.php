<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
// تأكد من استيراد Role إذا كنت ستستخدمه للتحقق
// use Spatie\Permission\Models\Role;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {
        // **التعديل:** مرر كلمة المرور كما هي (نص عادي) إلى الريبوزيتوري
        // الريبوزيتوري سيقوم بتشفيرها باستخدام Hash::make()
        $user = $this->userRepository->createUser([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // *** تمرير كلمة المرور بدون تشفير هنا ***
        ]);

        // **التعديل:** تفعيل وتأكيد منطق تعيين الدور
        // نفترض أن الريبوزيتوري لديه دالة assignRole كما هو موجود في الكود الذي قدمته
        if (isset($data['role'])) {
             // يمكنك إضافة تحقق إضافي هنا للتأكد من أن الدور المرسل موجود فعلاً
             // $roleExists = Role::where('name', $data['role'])->exists();
             // if ($roleExists) {
                $this->userRepository->assignRole($user, $data['role']);
             // } else {
             //     // التعامل مع حالة الدور غير الصالح إذا أردت
             // }
        } else {
             // يمكنك تعيين دور افتراضي إذا لم يتم إرسال دور
             // $this->userRepository->assignRole($user, 'user'); // مثال
        }


        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }

    public function login(string $email, string $password): ?array
    {
        // لا تغيير هنا، Auth::attempt يتوقع كلمة مرور نص عادي
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return null; // تسجيل الدخول فشل
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}