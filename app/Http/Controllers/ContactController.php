<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactReceived;
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Auth;


class ContactController extends Controller
{

    public function store(ContactRequest $request)
    {
        $validated = $request->validated();

        $contact = Contact::create($validated);

            // إرسال بريد إلكتروني
        Mail::to($contact->email)->send(new ContactReceived($contact));

          // الاستجابة
        return response()->json(['message' => 'تم إرسال الطلب بنجاح!'], 201);
    }
    public function getAllMessages()
    {
        // التحقق من أن المستخدم الحالي لديه دور "admin"
        if (Auth::user()->hasRole('admin')) {
            // استرجاع جميع الرسائل المرسلة
            $messages = Contact::all();

            // إرجاع النتيجة كـ JSON
            return response()->json(['messages' => $messages]);
        } else {
            // إذا لم يكن المستخدم الحالي لديه دور "admin"، قم بإرجاع رسالة خطأ
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    }
}
