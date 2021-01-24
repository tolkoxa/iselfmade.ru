'use strict';

class Reports {
    constructor() {
        this.skipBtnID = 'skip_report';
        this.nextBtnID = 'next_report';
        this.reportDataBlockID = 'report_data';
        this.countReports = 'count_reports';
        this.userId;
        this.date;
    }

    _post(url, data) {
        return $.post({
            url: url,
            data: data,
            success: function (data) {
                //data приходят те данные, который прислал на сервер
                if (data.result !== true) {
                    console.log('ERROR_POST_DATA');
                }
            }
        })
    }

    _renderReport(html, countReports, comments) {
        $('#' + this.countReports).text(countReports);
        return $('#' + this.reportDataBlockID).replaceWith(html) && $('#comments').replaceWith(comments);
    }

    _renderEnd() {
        let html = '<div class="check-content" id="report_data">' +
            '<p>Все отчеты проверены</p>' +
            '</div>';
        $('#' + this.countReports).text(0);
        return $('#' + this.reportDataBlockID).replaceWith(html);
    }

    _scrollCommentsDown(commentsBlock) {
        $(commentsBlock).scrollTop( $(commentsBlock).prop('scrollHeight'));
    }

    _changeReportStatus(status) {
        let sendData = {
            'user_id': this.userId,
            'date': this.date,
            'status': status,
        };

        this._post('/report/change-report-status', sendData)
            .then(data => {
                if (data.result === true && data.reportsCount >= 0) {
                    this._renderReport(data.nextReport, data.reportsCount, data.comments);
                    this._scrollCommentsDown(document.querySelector('.comments__block'));
                    modalImg2.init();
                    comment.init();
                } else {
                    this._renderEnd();
                }
            })
            .catch(error => {
            });
    }

    getData() {
        let reportBlock = document.getElementById(this.reportDataBlockID);
        this.userId = reportBlock.dataset.id;
        this.date = reportBlock.dataset.date;
    }

    init() {
        let nextBtn = document.getElementById(this.nextBtnID);
        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                this.getData();
                this._changeReportStatus(4);
            });
        }
        let skipBtn = document.getElementById(this.skipBtnID);
        if (skipBtn) {
            skipBtn.addEventListener('click', (e) => {
                this.getData();
                this._changeReportStatus(2);
            })
        }
    }
}

let reports = new Reports();
reports.init();

class UserReport {
    constructor() {
        this.reportFormID = 'report_data';
        this.dragDropID = 'drag_drop';
        this.inputFileID = 'files_input';
        this.userCommentID = 'user_comment';
        this.selectDayRatingID = 'day_rating';
        this.fileListId = 'file_list';
        this.sendReportBtnID = 'sendreport';
        this.files = [];
        this.userComment = '';
        this.dayRating = null;
        this.realFileList = [];
        this._csrf = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    }

    _renderInputFiles(files) {
        let inputBlock = document.getElementById(this.fileListId);
        let fileList = document.getElementById(this.fileListId);
        let html = '';
        Array.from(files).forEach((file, index) => {
            html += `<div class="input_file" id="file-${index}">
            <img class="input_img" src="" alt="${file.name}" title="${file.name}" id="upload_file-${index}">
                <span class="delete_file" data-id="${index}">x</span>
                </div>`;
            let reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('upload_file-' + index).setAttribute('src', e.target.result);
                };
            reader.readAsDataURL(file);
            reader.onerror = () => {
                console.log(reader.error);
            };
        });
        inputBlock.innerHTML = html;

        let delFileBtn = document.querySelectorAll('.delete_file');
        delFileBtn.forEach( (btn) => {
            let index = btn.dataset.id;
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                delete this.files[index];
                document.getElementById('file-'+ index).remove();
                if (fileList.innerHTML === '') {
                    fileList.innerText = 'Ничего не выбрано';
                }
            });
        });
    }

    disableReportForm() {
        let fileInput = document.getElementById(this.inputFileID);
        let dragDropID = document.getElementById(this.dragDropID);
        let reportForm = document.getElementById(this.reportFormID);
        let sendReport = document.getElementById(this.sendReportBtnID);
        sendReport.setAttribute('disabled','disabled');
    }

    init() {
        let fileInput = document.getElementById(this.inputFileID);
        let dragDropID = document.getElementById(this.dragDropID);
        let reportForm = document.getElementById(this.reportFormID);
        let sendReport = document.getElementById(this.sendReportBtnID);
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                this.files = [...fileInput.files];
                this.realFileList = fileInput.files;
                this._renderInputFiles(this.files);

            });
        }
        if (dragDropID) {
            reportForm.addEventListener('dragenter', (e) => {
                dragDropID.style.display = 'flex';
            });
            reportForm.addEventListener('dragover', (e) => {
                e.stopPropagation();
                e.preventDefault();
            });
            reportForm.addEventListener('dragleave', (e) => {
                if (e.target == dragDropID) {
                    dragDropID.removeAttribute('style');
                }
            });
            dragDropID.addEventListener('drop', (e) => {
                dragDropID.removeAttribute('style');
                e.stopPropagation();
                e.preventDefault();
                this.files = [...e.dataTransfer.files];
                this.realFileList = e.dataTransfer.files;
                this._renderInputFiles(this.files);
            });
        }

        if (sendReport) {
            sendReport.addEventListener('click', (e) => {
                e.stopPropagation(); // остановка всех текущих JS событий
                e.preventDefault();  // остановка дефолтного события для текущего элемента - клик для <a> тега
                let userComment = $('#'+this.userCommentID).val();
                let dayRating = $('#'+this.selectDayRatingID).val();
                let data = new FormData();
                let counter = 0;

                this.files.forEach( (file, index) => {
                    if (file ) {
                        data.append(`UsersReports[uploadFiles][${counter}]`, this.realFileList[index]);
                        counter++;
                    }
                });

                data.append('UsersReports[comment]', userComment);
                data.append('UsersReports[self_grade]', dayRating);
                data.append('_csrf', this._csrf);

                $.ajax({
                    url: 'report/add-report',
                    type: 'POST',
                    data: data,
                    cache: false,
                    dataType: 'json',
                    // отключаем обработку передаваемых данных, пусть передаются как есть
                    processData: false,
                    // отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
                    contentType: false,
                    success: (data) => {
                        if (data.result) {
                            // this.disableReportForm();
                            location.reload()
                        }
                        //data приходят те данные, который прислал на сервер
                        if (!data.result) {
                            console.log('ERROR_POST_DATA');
                        }
                    }

                })
            });
        }

    }
}

let userReport = new UserReport();
userReport.init();




