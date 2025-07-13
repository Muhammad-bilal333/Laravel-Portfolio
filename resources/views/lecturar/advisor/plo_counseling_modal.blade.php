<!-- PLO Counseling Modal -->
<div class="modal fade" id="ploCounselingModal" tabindex="-1" role="dialog" aria-labelledby="ploCounselingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ploCounselingModalLabel">PLO Counseling</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="failedPlosContent">
                    <!-- Failed PLOs will be loaded here -->
                </div>
                <div id="recommendedCourses" class="mt-4">
                    <!-- Recommended courses will be loaded here -->
                </div>
                <div id="courseRecommendationForm" class="mt-4">
                    <h6>Recommend Course</h6>
                    <div class="form-group">
                        <select class="form-control" id="recommendedCourseSelect">
                            <!-- Available courses will be loaded here -->
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary" id="sendRecommendation">
                        Send Recommendation
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> 