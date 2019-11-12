
(function($){
    $("#loglist").html("<h6 class='text-muted'>Пусто</h6>")
    $('#keyword_marks_allowed_cb').prop('checked', true);

    function _loadLogsContent(checked_logs) {
        $("#loglist").html("")

        $.post(
            "/getLogContent",
            {
                action: "getLogContent",
                logfiles: JSON.stringify(checked_logs),
                query_string: $("#query_string").val().trim(),
                allow_marks: $('#keyword_marks_allowed_cb').is(':checked'),
            },

      content_resp => {
        JSON.parse(content_resp)
        .forEach((file, file_index) => {
          file.lines = Object.values(file.lines)

          if(file.lines.length)
          $("#loglist").append(`
            <li>
              <h5>
                <div class='icon log-icon'></div>
                <span class='ml-1'>${file.filename}</span>
                <span class='float-right mr-2'>${file_index+1}</span>
              </h5>
              <table class='table table-sm table-hover table-stripped'>
                <thead class='thead-dark'>
                  <tr>
                    <th width='20'>№</th>
                    <th>Строка</th>
                  </tr>
                </thead>
                <tbody>
                  ${function(){
                    let tbody = ""
                    if (file.lines.length>0)
                      file.lines.forEach((line, line_index) => {
                        tbody += `<tr>
                          <td>${line_index}</td>
                          <td>${line}</td>
                        </tr>`
                      })
                    else
                      tbody = `<tr>
                        <td>...</td>
                        <td class="text-muted">Ничего не найдено</td>
                      </tr>`
                    return tbody
                  }()}
                </tbody>
              </table>
            </li>
          `)
        })
      })
  }

  $.post(
    "getLogfilesList", {
      action: 'getLogfilesList'
    },
    loglist_resp => {
      for (let filename of JSON.parse(loglist_resp)) {
        $("#logfiles-list").prepend(
          `<li>
            <label class='ml-3'>
              <input name='logfile' data-filename="${filename}" type='checkbox'>
              <span class="li-filename">${filename}</span>
            </label>
          </li>`
        )
      }

    }
  )

  $("#btn-filter").click(() => {
    // ОПРЕДЕЛЕНИЕ ВЫБРАННЫХ ЛОГОВ
    let checked_logs = []
    $('#logfiles-list li').each(function(){
      let checkbox = $(this).find('input[name="logfile"]:checkbox')
      if ($(checkbox).is(':checked')) {
        checked_logs.push($(checkbox).data("filename"))
      }
    })

    _loadLogsContent(checked_logs)
  })

})(jQuery)
