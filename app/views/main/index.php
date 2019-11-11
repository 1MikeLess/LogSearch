<div>
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

    <!-- <script src="<?php echo $_SERVER['DOCUMENT_ROOT'].'/public/script/js.js' ?>"></script> -->
    <script>
        <?php echo file_get_contents($_SERVER['DOCUMENT_ROOT'].'/public/script/js.js') ?>
    </script>
</div>
