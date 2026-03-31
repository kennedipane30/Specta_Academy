<h1>Hasil Tryout Spekta Academy</h1>
<p>Nama: {{ $result->user->name }}</p>
<p>Skor: {{ $result->score }}</p>
<hr>
@foreach($result->tryout->questions as $q)
    <p>{{ $q->question }}</p>
    <p>Jawaban Benar: {{ $q->correct_answer }}</p>
@endforeach
