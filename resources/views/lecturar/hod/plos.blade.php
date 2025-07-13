@extends('lecturar.dashboard')

@section('title', 'Program Learning Outcomes (PLOs)')

@section('content')
<div class="container mt-4">
    <div id="successMessage" class="alert alert-success" style="display: none;"></div>
    <h2 class="mb-4 text-center">Program Learning Outcomes (PLOs)</h2>
    <div class="mb-3 text-right">
        <button class="btn btn-success" data-toggle="modal" data-target="#addPloModal">Add PLO</button>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="plosTable" style="background: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.08);">
            <thead class="thead-dark">
                <tr>
                    <th style="width: 10%">PLO</th>
                    <th style="width: 20%">Title</th>
                    <th>Description</th>
                    <th style="width: 15%">Action</th>
                </tr>
            </thead>
            <tbody id="plosTbody">
                @foreach($plos as $index => $plo)
                <tr id="plo-row-{{ $index }}">
                    <td id="number-{{ $index }}">PLO {{ $index + 1 }}</td>
                    <td id="title-{{ $index }}">{{ $plo['title'] }}</td>
                    <td id="desc-{{ $index }}">{{ $plo['desc'] }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editPloModal{{ $index }}">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deletePlo({{ $index }})">Delete</button>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editPloModal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="editPloModalLabel{{ $index }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editPloModalLabel{{ $index }}">Edit PLO</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="form-group">
                                        <label for="ploNumber{{ $index }}">PLO Number</label>
                                        <input type="number" class="form-control" id="ploNumber{{ $index }}" value="{{ $index + 1 }}" min="1">
                                    </div>
                                    <div class="form-group">
                                        <label for="ploTitle{{ $index }}">Title</label>
                                        <input type="text" class="form-control" id="ploTitle{{ $index }}" value="{{ $plo['title'] }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="ploDesc{{ $index }}">Description</label>
                                        <textarea class="form-control" id="ploDesc{{ $index }}" rows="4">{{ $plo['desc'] }}</textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-success" onclick="savePlo({{ $index }})">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add PLO Modal -->
<div class="modal fade" id="addPloModal" tabindex="-1" role="dialog" aria-labelledby="addPloModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addPloModalLabel">Add New PLO</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="newPloNumber">PLO Number</label>
                        <input type="number" class="form-control" id="newPloNumber" min="1" oninput="validatePloNumber(this.value)" style="border-radius: 5px; border: 1px solid #ccc;">
                        <small id="ploNumberError" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label for="newPloTitle">Title</label>
                        <input type="text" class="form-control" id="newPloTitle" style="border-radius: 5px; border: 1px solid #ccc;">
                    </div>
                    <div class="form-group">
                        <label for="newPloDesc">Description</label>
                        <textarea class="form-control" id="newPloDesc" rows="4" style="border-radius: 5px; border: 1px solid #ccc;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="addPlo()" style="border-radius: 5px; background-color: #28a745; color: white; border: none; padding: 10px 20px;">Add</button>
            </div>
        </div>
    </div>
</div>

<style>
    .sidebar {
        min-height: 100vh;
        height: 100%;
    }
</style>

<script>
function showToast(message) {
    var successMessage = document.getElementById('successMessage');
    successMessage.innerText = message;
    successMessage.style.display = 'block';
    setTimeout(function() {
        successMessage.style.display = 'none';
    }, 3000);
}

function savePlo(index) {
    var newNumber = document.getElementById('ploNumber' + index).value;
    var newTitle = document.getElementById('ploTitle' + index).value;
    var newDesc = document.getElementById('ploDesc' + index).value;
    var table = document.getElementById('plosTbody');
    var rows = table.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        if (i !== index) {
            var cell = rows[i].getElementsByTagName('td')[0];
            if (cell && cell.innerText === 'PLO ' + newNumber) {
                alert('PLO number already exists.');
                return;
            }
        }
    }
    document.getElementById('number-' + index).innerText = 'PLO ' + newNumber;
    document.getElementById('title-' + index).innerText = newTitle;
    document.getElementById('desc-' + index).innerText = newDesc;
    $('#editPloModal' + index).modal('hide');
    showToast('PLO updated successfully.');
    // TODO: Add AJAX call here to save to backend if needed
}

function deletePlo(index) {
    var row = document.getElementById('plo-row-' + index);
    if (row) {
        row.parentNode.removeChild(row);
        showToast('PLO deleted successfully.');
    }
    // TODO: Add AJAX call here to delete from backend if needed
}

function addPlo() {
    var ploNumber = document.getElementById('newPloNumber').value;
    var title = document.getElementById('newPloTitle').value;
    var desc = document.getElementById('newPloDesc').value;
    if (!ploNumber || !title.trim() || !desc.trim()) {
        return;
    }
    var table = document.getElementById('plosTbody');
    var rows = table.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        var cell = rows[i].getElementsByTagName('td')[0];
        if (cell && cell.innerText === 'PLO ' + ploNumber) {
            alert('PLO number already exists.');
            return;
        }
    }
    var index = table.rows.length;
    var newRow = table.insertRow();
    newRow.id = 'plo-row-' + index;
    newRow.innerHTML = `
        <td id="number-${index}">PLO ${ploNumber}</td>
        <td id="title-${index}">${title}</td>
        <td id="desc-${index}">${desc}</td>
        <td>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editPloModal${index}">Edit</button>
            <button class="btn btn-danger btn-sm" onclick="deletePlo(${index})">Delete</button>
        </td>
    `;
    // Add modal for the new row
    var modalHtml = `
        <div class="modal fade" id="editPloModal${index}" tabindex="-1" role="dialog" aria-labelledby="editPloModalLabel${index}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPloModalLabel${index}">Edit PLO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="ploNumber${index}">PLO Number</label>
                                <input type="number" class="form-control" id="ploNumber${index}" value="${index + 1}" min="1">
                            </div>
                            <div class="form-group">
                                <label for="ploTitle${index}">Title</label>
                                <input type="text" class="form-control" id="ploTitle${index}" value="${title}">
                            </div>
                            <div class="form-group">
                                <label for="ploDesc${index}">Description</label>
                                <textarea class="form-control" id="ploDesc${index}" rows="4">${desc}</textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" onclick="savePlo(${index})">Save</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    $('#addPloModal').modal('hide');
    document.getElementById('newPloNumber').value = '';
    document.getElementById('newPloTitle').value = '';
    document.getElementById('newPloDesc').value = '';
    document.getElementById('ploNumberError').innerText = '';
    showToast('PLO added successfully.');
    // TODO: Add AJAX call here to save to backend if needed
}

function validatePloNumber(value) {
    var table = document.getElementById('plosTbody');
    var rows = table.getElementsByTagName('tr');
    var errorElement = document.getElementById('ploNumberError');
    for (var i = 0; i < rows.length; i++) {
        var cell = rows[i].getElementsByTagName('td')[0];
        if (cell && cell.innerText === 'PLO ' + value) {
            errorElement.innerText = 'PLO number already exists.';
            return;
        }
    }
    errorElement.innerText = '';
}
</script>
@endsection 