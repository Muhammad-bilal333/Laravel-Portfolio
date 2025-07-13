{{-- AssignCourceAdvisor.blade.php --}}
@extends('lecturar.dashboard')

@section('title', 'View Courses')

@section('content')
    <div class="container mt-4">
        <h2>Assign Faculty to Course</h2>

        @if(session('success'))
            <div  class="alert alert-success mt-4">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('student.store_marks') }}" >
            @csrf

            <!-- Assessment Type Dropdown -->
            <div class="form-group">
                <label for="type">Assessment Type</label>
                    <select id="type" name="type" class="form-control" required>
                        <option value="">Select Assessment Type</option>
                        <option value="Quiz">Quiz</option>
                        <option value="Assignment">Assignment</option>
                        <option value="Mid">Mid</option>
                        <option value="Final">Final</option>
                    </select>
            </div>


            

            <!-- Assessment Title -->
            <div class="form-group">
                <label for="assessment_title">Assessment Title</label>
                    <input id="assessment_title" type="text" class="form-control" name="assessment_title" required>
            </div>

            <input type="hidden" class="form-control" name="student_id" value="{{ $id }}">

            <!-- CLO Number Selection (Visible only for Mid/Final) -->
            <div class="form-group" id="clo-number-container" style="display: none;">
                <label for="clo_number">Number of CLOs</label>
                    <select id="clo_number" name="clo_number" class="form-control">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
            </div>

            <!-- Single CLO Fields (For Quiz/Assignment) -->
            <div id="single-clo-container">
                <div class="form-group">
                    <label for="clo_number_single">CLO</label>
                        <select id="clo_number_single" name="clo_number_single" class="form-control">
                            <option value="CLO1">CLO1</option>
                            <option value="CLO2">CLO2</option>
                            <option value="CLO3">CLO3</option>
                            <option value="CLO4">CLO4</option>
                        </select>
                </div>

                <div class="form-group">
                    <label for="total_marks_single" >Total Marks</label>
                        <input id="total_marks_single" type="number" class="form-control" name="total_marks_single">
                </div>

                <div class="form-group">
                    <label for="obtained_marks_single" >Obtained Marks</label>
                        <input id="obtained_marks_single" type="number" class="form-control" name="obtained_marks_single">
                </div>
            </div>

            <!-- Dynamic CLO Fields (For Mid/Final) -->
            <div id="dynamic-clo-container" style="display: none;">
                <!-- Fields will be added here by JavaScript -->
            </div>

            <div class="form-group row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                      Save
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const cloNumberContainer = document.getElementById('clo-number-container');
        const singleCloContainer = document.getElementById('single-clo-container');
        const dynamicCloContainer = document.getElementById('dynamic-clo-container');
        const cloNumberSelect = document.getElementById('clo_number');
    
        typeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            
            if (selectedType === 'Mid' || selectedType === 'Final') {
                cloNumberContainer.style.display = 'block';
                singleCloContainer.style.display = 'none';
                dynamicCloContainer.style.display = 'block';
                updateDynamicFields();
            } else {
                cloNumberContainer.style.display = 'none';
                singleCloContainer.style.display = 'block';
                dynamicCloContainer.style.display = 'none';
            }
        });
    
        cloNumberSelect.addEventListener('change', updateDynamicFields);
    
        function updateDynamicFields() {
            const cloCount = parseInt(cloNumberSelect.value);
            dynamicCloContainer.innerHTML = '';
    
            for (let i = 0; i < cloCount; i++) {
                const cloDiv = document.createElement('div');
                cloDiv.className = 'clo-group';
                cloDiv.innerHTML = `
                    <h5>CLO ${i+1}</h5>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Total Marks</label>
                        <div class="col-md-6">
                            <input type="number" class="form-control" name="total_marks[]" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Obtained Marks</label>
                        <div class="col-md-6">
                            <input type="number" class="form-control" name="obtained_marks[]" required>
                        </div>
                    </div>
                    <input type="hidden" name="clo_number[]" value="CLO${i+1}">
                `;
                dynamicCloContainer.appendChild(cloDiv);
            }
        }
    });
    </script>
    
    <style>
    .clo-group {
        background: #f8f9fa;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    </style>























