@extends('lecturar.dashboard')
@section('content')
<div class="container d-flex justify-content-center">
    <form action="{{ route('program_manager.course.update', $courses_detail->id) }}" method="POST" id="course-detail-form" style="margin-top:20px; width: 100%; max-width: 900px; max-height: 80vh; overflow-y: auto; overflow-x: auto; padding: 30px; background: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.08);">
        @csrf
        {{-- Course Title and Intro --}}
        @php
            $details = $courses_detail;
            $intro = $details->course_detail->first();
        @endphp

        <div class="mb-4">
            <label for="title"><strong>Course Title:</strong></label>
            <input type="text" class="form-control" name="title" value="{{ $intro->title ?? '' }}" required>
        </div>

        <div class="mb-4">
            <label for="intro_objectives"><strong>Course Introduction & Objectives:</strong></label>
            <textarea class="form-control" name="intro_objectives" rows="4" required>{{ $intro->intro_objectives ?? '' }}</textarea>
        </div>

        {{-- Course Outcomes --}}
        <h4 class="mt-5">Course Outcomes:</h4>
        <button type="button" class="btn btn-link text-success mb-2" id="add-outcome" title="Add Row">
            <i class="fas fa-plus"></i>
        </button>
        <table class="table table-bordered" id="outcomes-table">
            <thead>
                <tr>
                    <th>CLO</th>
                    <th>Description</th>
                    <th>Bloom's Level</th>
                    <th>PLO</th>
                </tr>
            </thead>
            <tbody>
                @php $outcomes = $courses_detail->course_outcome; @endphp
                @if($outcomes->count())
                    @foreach($outcomes as $index => $outcome)
                        <tr>
                            <td style="width: 15%;"><input type="text" name="course_outcomes[{{ $index }}][clo]" value="{{ $outcome->clo }}" class="form-control" required></td>
                            <td style="width: 55%;"><input type="text" name="course_outcomes[{{ $index }}][description]" value="{{ $outcome->description }}" class="form-control" required></td>
                            <td style="width: 15%;"><input type="text" name="course_outcomes[{{ $index }}][bloom]" value="{{ $outcome->blooms_level }}" class="form-control" required></td>
                            <td style="width: 15%;"><input type="text" name="course_outcomes[{{ $index }}][plo]" value="{{ $outcome->PLO }}" class="form-control" required></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="width: 15%;"><input type="text" name="course_outcomes[0][clo]" class="form-control" required></td>
                        <td style="width: 55%;"><input type="text" name="course_outcomes[0][description]" class="form-control" required></td>
                        <td style="width: 15%;"><input type="text" name="course_outcomes[0][bloom]" class="form-control" required></td>
                        <td style="width: 15%;"><input type="text" name="course_outcomes[0][plo]" class="form-control" required></td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Course Content --}}
        <h4 class="mt-5">Course Contents:</h4>
        <button type="button" class="btn btn-link text-success mb-2" id="add-content" title="Add Row">
            <i class="fas fa-plus"></i>
        </button>
        <div id="contents-section">
            @php $contents = $courses_detail->course_content; @endphp
            @if($contents->count())
                @foreach($contents as $cIndex => $content)
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" name="course_contents[{{ $cIndex }}][heading_number]" value="{{ $content->heading_number }}" class="form-control mb-2" placeholder="Heading Number" required>
                            </div>
                            <div class="col-md-10">
                                <input type="text" name="course_contents[{{ $cIndex }}][heading_title]" value="{{ $content->heading_title }}" class="form-control mb-2" placeholder="Heading Title" required>
                            </div>
                        </div>
                        <div class="points-container">
                            @foreach($courses_detail->course_content_point->where('course_contents_id', $content->id) as $pIndex => $point)
                                <textarea name="course_contents[{{ $cIndex }}][points][{{ $pIndex }}]" class="form-control mb-2" rows="2" required>{{ $point->description }}</textarea>
                            @endforeach
                            @if($courses_detail->course_content_point->where('course_contents_id', $content->id)->count() == 0)
                                <textarea name="course_contents[{{ $cIndex }}][points][0]" class="form-control mb-2" rows="2" required></textarea>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" name="course_contents[0][heading_number]" class="form-control mb-2" placeholder="Heading Number" required>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="course_contents[0][heading_title]" class="form-control mb-2" placeholder="Heading Title" required>
                        </div>
                    </div>
                    <div class="points-container">
                        <textarea name="course_contents[0][points][0]" class="form-control mb-2" rows="2" required></textarea>
                    </div>
                </div>
            @endif
        </div>

        {{-- Practical Outcomes --}}
        <h4 class="mt-5">Practical Outcomes:</h4>
        <button type="button" class="btn btn-link text-success mb-2" id="add-practical" title="Add Row">
            <i class="fas fa-plus"></i>
        </button>
        <table class="table table-bordered" id="practicals-table">
            <thead>
                <tr>
                    <th>CLO</th>
                    <th>Description</th>
                    <th>Bloom's Level</th>
                    <th>PLO</th>
                </tr>
            </thead>
            <tbody>
                @php $practicals = $courses_detail->practical_outcome; @endphp
                @if($practicals->count())
                    @foreach($practicals as $pIndex => $practical)
                        <tr>
                            <td style="width: 15%;"><input type="text" name="practical_outcomes[{{ $pIndex }}][clo]" value="{{ $practical->clo }}" class="form-control" required></td>
                            <td style="width: 55%;"><input type="text" name="practical_outcomes[{{ $pIndex }}][description]" value="{{ $practical->description }}" class="form-control" required></td>
                            <td style="width: 15%;"><input type="text" name="practical_outcomes[{{ $pIndex }}][bloom]" value="{{ $practical->blooms_level }}" class="form-control" required></td>
                            <td style="width: 15%;"><input type="text" name="practical_outcomes[{{ $pIndex }}][plo]" value="{{ $practical->PLO }}" class="form-control" required></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="width: 15%;"><input type="text" name="practical_outcomes[0][clo]" class="form-control" required></td>
                        <td style="width: 55%;"><input type="text" name="practical_outcomes[0][description]" class="form-control" required></td>
                        <td style="width: 15%;"><input type="text" name="practical_outcomes[0][bloom]" class="form-control" required></td>
                        <td style="width: 15%;"><input type="text" name="practical_outcomes[0][plo]" class="form-control" required></td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Submit Button --}}
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success">Save Changes</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Course Outcomes ---
    document.getElementById('add-outcome').onclick = function() {
        let table = document.getElementById('outcomes-table').getElementsByTagName('tbody')[0];
        let rowCount = table.rows.length;
        let row = table.insertRow();
        row.innerHTML = `<td><input type='text' name='course_outcomes[${rowCount}][clo]' class='form-control' required></td>
            <td><input type='text' name='course_outcomes[${rowCount}][description]' class='form-control' required></td>
            <td><input type='text' name='course_outcomes[${rowCount}][bloom]' class='form-control' required></td>
            <td><input type='text' name='course_outcomes[${rowCount}][plo]' class='form-control' required></td>
            <td><button type='button' class='btn btn-link text-danger remove-outcome' title='Delete Row'><i class='fas fa-trash'></i></button></td>`;
    };
    document.getElementById('outcomes-table').addEventListener('click', function(e) {
        if(e.target.closest('.remove-outcome')) {
            let row = e.target.closest('tr');
            row.parentNode.removeChild(row);
        }
    });

    // --- Course Contents ---
    document.getElementById('add-content').onclick = function() {
        let section = document.getElementById('contents-section');
        let cIndex = section.getElementsByClassName('content-row').length;
        let div = document.createElement('div');
        div.className = 'mb-3 content-row';
        div.innerHTML = `<div class='row'>
            <div class='col-md-2'><input type='text' name='course_contents[${cIndex}][heading_number]' class='form-control mb-2' placeholder='Heading Number' required></div>
            <div class='col-md-10'><input type='text' name='course_contents[${cIndex}][heading_title]' class='form-control mb-2' placeholder='Heading Title' required></div>
        </div>
        <div class='points-container'><textarea name='course_contents[${cIndex}][points][0]' class='form-control mb-2' rows='2' required></textarea></div>
        <button type='button' class='btn btn-link text-danger remove-content' title='Delete Row'><i class='fas fa-trash'></i></button>`;
        section.appendChild(div);
    };
    document.getElementById('contents-section').addEventListener('click', function(e) {
        if(e.target.closest('.remove-content')) {
            let row = e.target.closest('.content-row');
            row.parentNode.removeChild(row);
        }
    });

    // --- Practical Outcomes ---
    document.getElementById('add-practical').onclick = function() {
        let table = document.getElementById('practicals-table').getElementsByTagName('tbody')[0];
        let rowCount = table.rows.length;
        let row = table.insertRow();
        row.innerHTML = `<td><input type='text' name='practical_outcomes[${rowCount}][clo]' class='form-control' required></td>
            <td><input type='text' name='practical_outcomes[${rowCount}][description]' class='form-control' required></td>
            <td><input type='text' name='practical_outcomes[${rowCount}][bloom]' class='form-control' required></td>
            <td><input type='text' name='practical_outcomes[${rowCount}][plo]' class='form-control' required></td>
            <td><button type='button' class='btn btn-link text-danger remove-practical' title='Delete Row'><i class='fas fa-trash'></i></button></td>`;
    };
    document.getElementById('practicals-table').addEventListener('click', function(e) {
        if(e.target.closest('.remove-practical')) {
            let row = e.target.closest('tr');
            row.parentNode.removeChild(row);
        }
    });
});
</script>
@endpush

{{-- Add these icon buttons in your Blade where you want the add row actions --}}
{{-- Example: --}}
{{-- <button type="button" class="btn btn-link text-success" id="add-outcome" title="Add Row"><i class="fas fa-plus"></i></button> --}}
{{-- <button type="button" class="btn btn-link text-success" id="add-content" title="Add Row"><i class="fas fa-plus"></i></button> --}}
{{-- <button type="button" class="btn btn-link text-success" id="add-practical" title="Add Row"><i class="fas fa-plus"></i></button> --}}
