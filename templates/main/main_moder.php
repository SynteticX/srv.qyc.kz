<?php 
// Настройки
if (isset($_POST['start_day'])) {
	set_setting('start_day',intval($_POST['start_day']));
}
if (isset($_POST['salary'])) {
	set_setting('salary_' . date('Y'),intval($_POST['salary']));
}
if (isset($_POST['gu_salary'])) {
	set_setting('gu_salary_' . date('Y'),intval($_POST['gu_salary']));
}
$start_day = get_setting('start_day');
$salary = get_setting('salary_' . date('Y'));
$gu_salary = get_setting('gu_salary_' . date('Y'));
?>
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Панель управления</h4>
        </div>
    </div>
</div>
<!-- end page title -->
<div class="row">

</div>
<div class="row">
    <div class="col">
        <div class="card d-flex">
            <div class="card-body row">
                <div class="col-8">
                    <i class="h5 fa fa-cog"></i><span class="h5"> Настройки</span>
                    <hr>
                    <div class="d-flex">
                        <!-- Дата начала периода -->
                        <form id="startDate" method="post" class="col">
                            <label for="start_day" class="form-label">Дата начала периода</label>
                            <select class="form-select" name="start_day" id="start_day">
                                <?php
                                $i = 1;
                                while ($i <= 28) {?>
                                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                <?php
                                $i += 1;
                                }
                                ?>
                            </select>
                            <p></p>
                            <button class="btn btn-primary zoom" type="submit"><span class="fa fa-save" style="color:white"></span> Сохранить</button>
                        </form>
                        <script>
                        document.addEventListener("DOMContentLoaded", function(){
                            $('#start_day').val("<?php echo $start_day; ?>").change();
                        });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>