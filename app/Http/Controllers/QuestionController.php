<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $city = City::all();
        $question_category = QuestionCategory::all();
        $data = Question::join('answers', 'answers.question_id', '=', 'questions.id')->get();
        return view('konsultasi.ask.index', compact('city', 'question_category', 'data'));
    }

    public function all(Request $request)
    {
        if ($request->has('q')) {
            $data = Question::where('name', 'like', '%' . $request->q . '%')
                ->orWhere('wa', 'like', '%' . $request->q . '%')
                ->orWhere('nip', 'like', '%' . $request->q . '%')
                ->orWhere('instansi', 'like', '%' . $request->q . '%')
                ->join('answers', 'answers.question_id', '=', 'questions.id')->paginate(5);
        } else {
            $data = Question::join('answers', 'answers.question_id', '=', 'questions.id')
                ->latest('questions.created_at')->paginate(5);
        }

        $category = QuestionCategory::with(['question'])->get();
        $city = City::with(['question'])->get();
        return view('konsultasi.ask.all', compact('data', 'category', 'city'));
    }

    public function allCategory($id)
    {
        $kategori = QuestionCategory::find($id);
        $data = Question::with(['question_category'])->where('question_category_id', $id)
            ->join('answers', 'answers.question_id', '=', 'questions.id')
            ->latest('questions.created_at')->paginate(5);
        $category = QuestionCategory::with(['question'])->get();
        $city = City::with(['question'])->get();
        return view('konsultasi.category.index', compact('data', 'category', 'city', 'kategori'));
    }

    public function allCity($id)
    {
        $kota = City::find($id);
        $data = Question::with(['question_category'])->where('city_id', $id)
            ->join('answers', 'answers.question_id', '=', 'questions.id')
            ->latest('questions.created_at')->paginate(5);
        $category = QuestionCategory::with(['question'])->get();
        $city = City::with(['question'])->get();
        return view('konsultasi.city.index', compact('data', 'category', 'city', 'kota'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'nip' => 'numeric|max_digits:18',
            'instansi' => 'required|string',
            'wa' => 'required|numeric|max_digits:13',
            'pesan' => 'required|string',
        ], [
            'required' => 'Data Tidak Boleh Kosong',
            'string' => 'Data Harus Di isi Huruf',
            'numeric' => 'Data Harus Di isi Angka',
            'max_digits' => 'Data Harus Lebih Dari',
        ]);

        Question::create([
            'name' => $request->name,
            'nip' => $request->nip,
            'city_id' => $request->city_id,
            'question_category_id' => $request->question_category_id,
            'instansi' => $request->instansi,
            'wa' => $request->wa,
            'pesan' => $request->pesan,
        ]);

        return redirect('/')->with('toast_success', 'Selamat Pertanyaan Anda Sudah Kami Terima');
    }
}
