<div style="background-color:#f7b2b2; margin:12px; padding:20px; border-radius:12px">
    <p style="border-left:2px solid red; padding-left:12px">
        <span>Путь: <b><?php echo $_SERVER['REQUEST_URI'] ?></b> </span></br>
        <span>Произошла ошибка! </span></br>
        <span>Код ошибки: <b><?php echo $code ?></b></span>
    </p>
    <p><?php echo $error_content ?></p>
</div>
