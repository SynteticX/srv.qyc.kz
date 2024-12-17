<?php
    $client = $clients[array_search($client_id, array_column($clients, 'id'))];

    echo <<<HTML
<div class="d-flex justify-content-between">
    <a class="btn btn-outline-dark" href="/clients.php"><i class="fa fa-chevron-left"></i> Назад</a>
    <a class="btn btn-outline-dark" id="editDossier" href="#" onclick="editDossier()"><i class="fa fa-pen"></i> Редактировать данные</a>
</div>

<div class="container my-5">
    <h2 class="text-center">Досье клиента {$client["name"]}</h2>
    
    <!-- Фото -->
    <div class="row mb-4 text-center">
        <div class="col-12 list-group-item">
            <span data-type="photo"><img src="https://via.placeholder.com/150" alt="Фото" class="rounded-circle img-thumbnail"></span>
        </div>
    </div>

    <!-- Личная информация -->
    <div class="mb-4">
        <h4>1. Личная информация</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Полное имя:</strong> <span data-type="name"></span></li>
            <li class="list-group-item"><strong>Дата рождения:</strong> <span data-type="birth" data-input="date"></span></li>
            <li class="list-group-item"><strong>Гражданство:</strong> <span data-type="country"></span></li>
            <li class="list-group-item"><strong>Семейное положение:</strong> <span data-type="family"></span></li>
            <li class="list-group-item"><strong>Адрес проживания:</strong> <span data-type="address"></span></li>
        </ul>
    </div>

    <!-- Образование -->
    <div class="mb-4">
        <h4>2. Образование</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Учебные заведения:</strong> <span data-type="learn_places"></span></li>
            <li class="list-group-item"><strong>Полученные степени и дипломы:</strong> <span data-type="diploma"></span></li>
            <li class="list-group-item"><strong>Даты учебы:</strong> <span data-type="learn_years" data-input="date"></span></li>
            <li class="list-group-item"><strong>Образовательные учреждения, которые человек посещал 
                (школы для слабослышащих, специализированные колледжи):</strong> <span data-type="learn_places_2"></span></li>
            <li class="list-group-item"><strong>Дополнительные программы обучения и курсы:</strong> <span data-type="courses"></span></li>
        </ul>
    </div>

    <!-- Профессиональная деятельность -->
    <div class="mb-4">
        <h4>3. Профессиональная деятельность</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Рабочий стаж и опыт:</strong> <span data-type="work_exp"></span></li>
            <li class="list-group-item"><strong>Места работы:</strong> <span data-type="work_place"></span></li>
            <li class="list-group-item"><strong>Должности:</strong> <span data-type="job_names"></span></li>
            <li class="list-group-item"><strong>Достижения и награды:</strong> <span data-type="advancements"></span></li>
            <li class="list-group-item"><strong>Рабочие места, которые адаптированы под 
                нужды человека с нарушением слуха:</strong> <span data-type="workplace_for_WC"></span></li>
            <li class="list-group-item"><strong>Специализированные навыки и компетенции, 
                полученные на курсах и тренингах:</strong> <span data-type="spec_skills"></span></li>
        </ul>
    </div>

    <!-- Финансовое состояние -->
    <div class="mb-4">
        <h4>4. Финансовое состояние</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Уровень дохода:</strong> <span data-type="salary"></span></li>
            <li class="list-group-item"><strong>Имущество:</strong> <span data-type="property"></span></li>
            <li class="list-group-item"><strong>Банковские счета и кредиты:</strong> <span data-type="bank_info"></span></li>
        </ul>
    </div>

    <!-- Судимости и правонарушения -->
    <div class="mb-4">
        <h4>5. Судимости и правонарушения</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Уголовные дела:</strong> <span data-type="ugolov_dela"></span></li>
            <li class="list-group-item"><strong>Административные правонарушения:</strong> <span data-type="adm_dela"></span></li>
            <li class="list-group-item"><strong>Штрафы и наказания:</strong> <span data-type="fines"></span></li>
        </ul>
    </div>

    <!-- Связи и контакты -->
    <div class="mb-4">
        <h4>6. Связи и контакты</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Друзья и знакомые:</strong> <span data-type="friends"></span></li>
            <li class="list-group-item"><strong>Родственники:</strong> <span data-type="relatives"></span></li>
            <li class="list-group-item"><strong>Профессиональные контакты:</strong> <span data-type="prof_contacts"></span></li>
        </ul>
    </div>

    <!-- Онлайн активность -->
    <div class="mb-4">
        <h4>7. Онлайн активность</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Профили в социальных сетях:</strong> <span data-type="socials_links"></span></li>
            <li class="list-group-item"><strong>Публикации и комментарии:</strong> <span data-type="socials_comments"></span></li>
            <li class="list-group-item"><strong>Электронная почта и переписка:</strong> <span data-type="socials_messages"></span></li>
        </ul>
    </div>

    <!-- Медицинская информация -->
    <div class="mb-4">
        <h4>8. Медицинская информация</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Диагнозы и медицинские заключения:</strong> <span data-type="med_diagnos"></span></li>
            <li class="list-group-item"><strong>Степень нарушения слуха:</strong> <span data-type="med_deaf_stepen"></span></li>
            <li class="list-group-item"><strong>Использование слуховых аппаратов или кохлеарных имплантов:</strong> <span data-type="med_implants"></span></li>
            <li class="list-group-item"><strong>Информация о пройденных операциях и медицинских процедурах:</strong> <span data-type="med_surgery_operations"></span></li>
            <li class="list-group-item"><strong>План лечения и рекомендации врачей:</strong> <span data-type="med_plan"></span></li>
        </ul>
    </div>

    <!-- Социальная информация -->
    <div class="mb-4">
        <h4>9. Социальная информация</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Степень социального взаимодействия и интеграции:</strong> <span data-type="soc_relations"></span></li>
            <li class="list-group-item"><strong>Информация о поддержке и помощи, получаемой от социальных служб:</strong> <span data-type="soc_help"></span></li>
            <li class="list-group-item"><strong>Участие в специализированных организациях и сообществах для людей с нарушением слуха:</strong> <span data-type="soc_organisations"></span></li>
        </ul>
    </div>

    <!-- Помощь и поддержка -->
    <div class="mb-4">
        <h4>10. Помощь и поддержка</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Список вспомогательных технологий и устройств, используемых для улучшения качества жизни (например, видеотелефоны, слуховые аппараты):</strong> <span data-type="help_devices"></span></li>
            <li class="list-group-item"><strong>Информация о социальных работниках, кураторах и волонтерах:</strong> <span data-type="help_volounteers"></span></li>
        </ul>
    </div>

    <!-- Коммуникация -->
    <div class="mb-4">
        <h4>11. Коммуникация</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Используемые методы коммуникации (жестовый язык, письменная коммуникация, устная речь с помощью слуховых аппаратов):</strong> <span data-type="communication"></span></li>
            <li class="list-group-item"><strong>Специалисты по жестовому языку или переводчики, с которыми человек взаимодействует:</strong> <span data-type="communicate_specialists"></span></li>
        </ul>
    </div>

    <!-- Личное развитие и интересы -->
    <div class="mb-4">
        <h4>12. Личное развитие и интересы</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Хобби и увлечения, которые способствуют социальному взаимодействию и личному развитию:</strong> <span data-type="hobby"></span></li>
            <li class="list-group-item"><strong>Участие в культурных и спортивных мероприятиях, адаптированных для людей с нарушением слуха:</strong> <span data-type="hobby_org"></span></li>
        </ul>
    </div>

    <!-- Открытие собственного дела -->
    <div class="mb-4">
        <h4>13. Открытие собственного дела</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Опыт в открытии собственного дела:</strong> <span data-type="business_exp"></span></li>
            <li class="list-group-item"><strong>Открывал ли он ИП (индивидуальный предприниматель) или ТОО (товарищество с ограниченной ответственностью):</strong> <span data-type="was_ip"></span></li>
        </ul>
    </div>

    <!-- Дополнительная информация -->
    <div class="mb-4">
        <h4>14. Дополнительная информация</h4>
        <ul class="list-group">
            <li class="list-group-item"><strong>Политические и религиозные взгляды:</strong> <span data-type="policy"></span></li>
        </ul>
    </div>

</div>

<script>window.client_id = {$client_id};</script>
HTML;
?>

<script src="templates/clients/clients_dossier.js?v=<?php echo time(); ?>"></script>