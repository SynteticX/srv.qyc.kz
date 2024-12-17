window.Apex = {
    dataLabels: {
      enabled: false
    }
  };
  // Текущий год
  let currentYear = new Date().getFullYear();
  // Количество специалистов по месяцам
  let specialists = [];
  // Количество заработка по месяцам
  let money = [];

  // Получаем работавших в месяц специалистов
  function getSpecialistsForMonth(forms, month) {
    let specialists = [];
    if (forms == null) {
      return [];
    }
    for (let form of forms) {
      if (parseInt(form.month) == parseInt(month)) {
        specialists.push(form.spec_id);
      }
    }
    specialists = [...new Set(specialists)];
    return specialists;
  }
  // Считаем специалистов за год (в массиве)
  function getSpecialistCountForYear(forms) {
    let result = [];
    for (let i = 1; i < 13; i++) {
      specialists_count = getSpecialistsForMonth(forms, i).length;
      if (specialists_count == null) {
        result.push(0);
      } else {
        result.push(specialists_count);
      }
    }
    // console.log(result);
    return result;
  }
  // Считает часы за месяц
  function getHoursForMonth(forms, month) {
    let hours = [];
    if (forms == null) {
      return [0];
    }
    for (let form of forms) {
      if (parseInt(form.month) == parseInt(month)) {
        hours.push(countHoursByForm(form));
      }
    }
    return hours;
  }
  // Считает доход за год (в массиве)
  async function getMoneyForYear(forms) {
    let result = [];
    await getFormAsync({
      'get_setting': 'full_tariff_' + currentYear
    }).then((salary) => {
      for (let i = 1; i < 13; i++) {
        let temp_result = 0;
        let month_result = getHoursForMonth(forms, i);
        // Получаем тариф на этот год и высчитываем сумму из часов * тариф
        
          for (let hours of month_result) {
            temp_result += hours;
          }
          // console.log(temp_result);

        
          temp_result = Math.floor(temp_result * parseInt(JSON.parse(salary)));
          result.push(parseInt(temp_result));
      }
    })
    return result;
  }
  // Считает сумму на мероприятия
  // Шаблон: [Общая сумма, [янв, фев, ...]]
  function countMoneyForHoliday(moneyArray, holidayTariff) {
    let result = [];
    for (let month in moneyArray) {
      result[0] += month * holidayTariff;
      result[1].push(month * holidayTariff);
    }
    return result;
  }

  // Скачиваем формы за текущий год
  async function getForms() {
    let params = {
        "getforms": 0,
        "year": currentYear
    }
    let forms = await getFormAsync(params);
    return forms;
  }
  // Код после загрузки всех форм за год
  getForms().then(async (forms) => {
    forms = JSON.parse(forms);
    // Формируем результат вычислений для дашборда
    result = [await getMoneyForYear(forms), await getSpecialistCountForYear(forms)];
    return result;
  })
  .then((data) => {
    var optionsBar = {
      chart: {
          type: 'polarArea',
          height: 300,
          width: '100%',
          group: 'stats'
      },
      plotOptions: {
          polarArea: {
              dataLabels: {
                  enabled: true  // Включено отображение данных для лучшей читаемости
              },
              startAngle: -90,
              endAngle: 270
          }
      },
      legend: {
        show: false
      },
      colors: ["#00C5A4", '#fcb92c', '#ff3d60'],
      series: data[1],
      labels: [
          "Январь",
          "Февраль",
          "Март",
          "Апрель",
          "Май",
          "Июнь",
          "Июль",
          "Август",
          "Сентябрь",
          "Октябрь",
          "Ноябрь",
          "Декабрь"
      ],
      title: {
          text: 'Количество специалистов',
          align: 'left',
      },
      subtitle: {
          text: 'за год'
      },
      markers: {
        size: 0
      },
      tooltip: {
        fixed: {
          enabled: true,
          position: 'right'
        },
        x: {
          show: false
        }
      },
  }
  

    ///////////////////////////////////////////////
    // График дохода
    var optionsBar2 = {
      chart: {
        type: 'polarArea',
        height: 300,
        width: '100%',
        group: 'stats'
      },
      plotOptions: {
          polarArea: {
              dataLabels: {
                  enabled: true  // Включено отображение данных для лучшей читаемости
              },
              startAngle: -90,
              endAngle: 270
          }
      },
      colors: ["#00C5A4", '#fcb92c', '#ff3d60'],
      series: data[0],
      labels: [
        "Январь",
        "Февраль",
        "Март",
        "Апрель",
        "Май",
        "Июнь",
        "Июль",
        "Август",
        "Сентябрь",
        "Октябрь",
        "Ноябрь",
        "Декабрь"
      ],
      title: {
        text: 'Доход',
        align: 'left',
      },
      subtitle: {
        text: 'за год'
      },  
      legend: {
        show: false
      }  
    }

    console.log(data)
    console.log(typeof data[0][0])
    console.log(typeof data[1][0])

    return [optionsBar, optionsBar2];
  })
  .then((bars) => {
    var chartBar = new ApexCharts(document.querySelector('#bar'), bars[0]);
    var chartBar2 = new ApexCharts(document.querySelector('#chart'), bars[1]);
    chartBar.render();
    chartBar2.render();
  })
  
