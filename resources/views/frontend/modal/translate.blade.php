<div class="box-search" id="modal-translate">
    <div class="header-translate">
        <span class="icon-search"><i class="fas fa-search"></i></span>
        <input class="text-translate" name="" placeholder="{{ trans('label.enter_search_word') }}">
        <button type="button" class="close" id="close-search">
            <span><i class="fas fa-times"></i></span>
        </button>
    </div>
    <div class="content">
        <ul class="nav nav-pills">
            <li class="nav-item tab-word">
                <a class="nav-link active" data-toggle="pill" href="#vocabulary">
                    <span>Vocabulary</span>
                </a>
            </li>
            <li class="nav-item tab-kanji">
                <a class="nav-link" data-toggle="pill" href="#kanji">
                    <span>Kanji</span>
                </a>
            </li>
            <li class="nav-item tab-sentence">
                <a class="nav-link" data-toggle="pill" href="#sentence">
                    <span>Sentence</span>
                </a>
            </li>
        </ul>
                
        <div class="tab-content">
            <div class="tab-pane active" id="vocabulary">
                <div class="word-translate">

                </div>
            </div>
            <div class="tab-pane fade" id="kanji">
                <div class="kanji-translate">
                    <div class="box-kanji">
                    </div>
                    <div class="box-kanji-list">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="sentence">
                <div class="exam-translate">

                </div>
            </div>
        </div>
    </div>
</div>