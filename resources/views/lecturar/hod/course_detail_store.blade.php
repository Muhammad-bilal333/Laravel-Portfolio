@extends('lecturar.dashboard')
@section('content')
<div class="container d-flex justify-content-center">
    {{-- <form action="{{ route('course.update', $courses_detail->id) }}" method="POST" style="margin-top:20px; width: 100%; max-width: 900px; height: 700px; overflow-y: auto; padding: 20px; border: 1px solid #ccc; background-color: #fff; border-radius: 8px;"> --}}
        <form action="{{ route('lecturer.course.save') }}" method="POST">
            @csrf
        {{-- {{ dd($Course); }} --}}
            <label for="course_id" class="form-label">Select Course</label>
            <select name="course_id" id="course_id" class="form-control" required onchange="updateCourseName()">
                <option value="" disabled selected>-- Select a Course --</option>
                @foreach($Course as $course)
                    <option value="{{ $course->id }}" data-name="{{ $course->name }}">{{ $course->code }} - {{ $course->name }}</option>
                @endforeach
            </select>
            
            <!-- Hidden field for course name -->
            <input type="hidden" name="course_name" id="course_name">
            

            {{-- Intro Objectives --}}
            <div class="mb-4">
                <label for="intro_objectives" class="form-label">Introduction / Objectives</label>
                <textarea name="intro_objectives" class="form-control" rows="4" required></textarea>
            </div>
        
            {{-- Course Outcomes --}}
            <h4>Course Outcomes</h4>
            @for($i = 0; $i < 3; $i++)
                <div class="row mb-2">
                    <div class="col-md-3">
                        <input type="text" name="course_outcomes[{{ $i }}][clo]" class="form-control" placeholder="CLO">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="course_outcomes[{{ $i }}][description]" class="form-control" placeholder="Description">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="course_outcomes[{{ $i }}][bloom]" class="form-control" placeholder="Bloom’s Level">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="course_outcomes[{{ $i }}][plo]" class="form-control" placeholder="PLO">
                    </div>
                </div>
            @endfor
        
            {{-- Course Contents --}}
            {{-- <h4 class="mt-4">Course Contents</h4>
            @for($i = 0; $i < 2; $i++)
                <div class="mb-3 border p-3">
                    <input type="text" name="course_contents[{{ $i }}][heading_number]" class="form-control mb-2" placeholder="Heading Number">
                    <input type="text" name="course_contents[{{ $i }}][heading_title]" class="form-control mb-2" placeholder="Heading Title">
        
                    @for($j = 0; $j < 2; $j++)
                        <textarea name="course_contents[{{ $i }}][points][{{ $j }}]" class="form-control mb-2" rows="2" placeholder="Point {{ $j + 1 }}"></textarea>
                    @endfor
                </div>
            @endfor --}}

            <!-- Select Number of Headings -->
            <div class="mb-3">
                <label for="headingCount" class="form-label">Number of Headings</label>
                <input type="number" id="headingCount" min="1" class="form-control" placeholder="Enter number of headings">
            </div>

            <!-- Dynamic Container for Course Contents -->
            <div id="headingsContainer"></div>

            <!-- Hidden Template for Headings -->
            <template id="headingTemplate">
                <div class="heading-block mb-4 border p-3">
                    <input type="text" name="heading_number" class="form-control mb-2" placeholder="Heading Number">
                    <input type="text" name="heading_title" class="form-control mb-2" placeholder="Heading Title">

                    <label class="form-label">Number of Points</label>
                    <input type="number" class="form-control mb-2 point-count" placeholder="Enter number of points">

                    <div class="points-container"></div>
                </div>
            </template>
        
            {{-- Practical Outcomes --}}
            <h4 class="mt-4">Practical Outcomes</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width:15%">CLO</th>
                        <th style="width:55%">Description</th>
                        <th style="width:15%">Bloom’s Level</th>
                        <th style="width:15%">PLO</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 3; $i++)
                        <tr>
                            <td><input type="text" name="practical_outcomes[{{ $i }}][clo]" class="form-control"></td>
                            <td><input type="text" name="practical_outcomes[{{ $i }}][description]" class="form-control"></td>
                            <td><input type="text" name="practical_outcomes[{{ $i }}][bloom]" class="form-control"></td>
                            <td><input type="text" name="practical_outcomes[{{ $i }}][plo]" class="form-control"></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        
            <button type="submit" class="btn btn-primary mt-4">Save Course Details</button>
        </form>
        
</div>

<script>
    document.getElementById('headingCount').addEventListener('input', function () {
        const count = parseInt(this.value);
        const container = document.getElementById('headingsContainer');
        const template = document.getElementById('headingTemplate');
        container.innerHTML = '';

        for (let i = 0; i < count; i++) {
            const clone = template.content.cloneNode(true);
            // Set proper name attributes
            clone.querySelector('input[name="heading_number"]').setAttribute('name', `course_contents[${i}][heading_number]`);
            clone.querySelector('input[name="heading_title"]').setAttribute('name', `course_contents[${i}][heading_title]`);

            // Attach listener for points input
            const pointCountInput = clone.querySelector('.point-count');
            const pointsContainer = clone.querySelector('.points-container');

            pointCountInput.addEventListener('input', function () {
                const pCount = parseInt(this.value);
                pointsContainer.innerHTML = '';
                for (let j = 0; j < pCount; j++) {
                    const textarea = document.createElement('textarea');
                    textarea.className = 'form-control mb-2';
                    textarea.placeholder = `Point ${j + 1}`;
                    textarea.name = `course_contents[${i}][points][${j}]`;
                    textarea.rows = 2;
                    pointsContainer.appendChild(textarea);
                }
            });

            container.appendChild(clone);
        }
    });
</script>
<script>
    function updateCourseName() {
        const select = document.getElementById('course_id');
        const selectedOption = select.options[select.selectedIndex];
        const courseName = selectedOption.getAttribute('data-name');
        document.getElementById('course_name').value = courseName;
    }
</script>
@endsection
