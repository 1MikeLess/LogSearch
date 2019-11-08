<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Поиск логов</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="/img/logfile.png" type="img/png">
  </head>
  <body>
    <div class="wrapper">

      <!-- МЕНЮ ПОИСКА -->
      <header class="header">

        <form>
          <div class="clearfix">
            <div class="header-logfiles d-inline-block float-left pr-3 mr-3">
              <h6 class="ml-1">Лог-файлы: </h6>
              <ul id="logfiles-list" class="header-logfiles-list">
              </ul>
            </div>

            <div class="header-search d-inline-block">
              <label>
                <h6>Поисковая фраза</h6>
                <input
                  class="form-control"
                  type="text"
                  id="query_string"
                  placeholder="По умолчанию вывести всё">
              </label>
            </div>
          </div>

          <div>
            <button id="btn-filter" type="button" class="ml-1 btn btn-info btn-md">
              <span>Фильтр</span>
              <!-- <div class="icon filter-icon"></div> -->
            </button>
          </div>
        </form>

      </header>

      <!-- ТАБЛИЦА ВЫВОДА РЕЗУЛЬТАТА ПОИСКА -->
      <div class="content">
        <ul id="loglist" class="loglist">
        </ul>
      </div>

      <footer class="footer">
        <h6 id="logdir-string" class="text-muted" />
      </footer>

    </div>

    <script src="js/js.js"></script>
  </body>
</html>
