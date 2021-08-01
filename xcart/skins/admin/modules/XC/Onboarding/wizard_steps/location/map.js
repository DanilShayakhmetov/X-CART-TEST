/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Location map
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

$(document).ready(function () {
  google.charts.load('current', {
    'packages': ['geochart'],
    // Note: you will need to get a mapsApiKey for your project.
    // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
    'mapsApiKey': 'AIzaSyAZnZiHuPoRL15Dt3FuvU2byNbtAaem4Vk'
  });
  google.charts.setOnLoadCallback(drawRegionsMap);

  function restartAnimation(element) {
    element.style.animation = 'none';
    element.offsetHeight; /* trigger reflow */
    element.style.animation = null;
  }

  function drawRegionsMap() {
    var data = google.visualization.arrayToDataTable([]);

    var options = {
      'datalessRegionColor': '#d8d8d8',
      'enableRegionInteractivity': false,
      'legend': 'none',
      'resolution': 'continents',
      'colorAxis': {'colors': ['#97c564', '#97c564']},
      'keepAspectRatio': false,
      'width': 250,
      'height': 140
    };

    var chart = new google.visualization.GeoChart(document.getElementById('location_map_zones'));

    chart.draw(data, options);

    core.bind('updateLocationMap', function (event, params) {
      var code = params.code;
      var name = params.name;

      $('#location_map .pulse').hide();

      var data = google.visualization.arrayToDataTable([
        ['Continent', 'Popularity'],
        ['0', 1]
      ]);

      chart.draw(data, options);

      var country = window.onboardingCountryData[code];

      if (country) {
        $('#location_map .point, #location_map .pulse').css({
          top: country.cy,
          left: country.cx
        }).show();

        restartAnimation($('#location_map .point').get(0));
      }
    });
    core.trigger('location_map_ready');
  }

  window.onboardingCountryData = {
    "AF": {
      "name": "Afghanistan",
      "cx": "170.29961659544682px",
      "cy": "77.46126664681358px"
    },
    "AX": {
      "name": "Åland Islands",
      "cx": "138.32403110535688px",
      "cy": "53.63123364950591px"
    },
    "AL": {
      "name": "Albania",
      "cx": "138.4931073083164px",
      "cy": "71.99117668425951px"
    },
    "DZ": {
      "name": "Algeria",
      "cx": "126.1103304338704px",
      "cy": "81.59950954238442px"
    },
    "AS": {
      "name": "American Samoa",
      "cx": "11.177380737018808px",
      "cy": "107.80019697145882px"
    },
    "AD": {
      "name": "Andorra",
      "cx": "126.01812213389908px",
      "cy": "70.90111043764865px"
    },
    "AO": {
      "name": "Angola",
      "cx": "136.9580680874249px",
      "cy": "105.91156203673391px"
    },
    "AI": {
      "name": "Anguilla",
      "cx": "82.80555218069868px",
      "cy": "88.01607291578223px"
    },
    "AQ": {
      "name": "Antarctica",
      "cx": "undefinedpx",
      "cy": "undefinedpx"
    },
    "AG": {
      "name": "Antigua and Barbuda",
      "cx": "83.65667643620826px",
      "cy": "88.74664392135547px"
    },
    "AR": {
      "name": "Argentina",
      "cx": "82.43888862722598px",
      "cy": "124.15645749735658px"
    },
    "AM": {
      "name": "Armenia",
      "cx": "155.1316513076485px",
      "cy": "72.84858953231335px"
    },
    "AW": {
      "name": "Aruba",
      "cx": "78.18946853765162px",
      "cy": "91.56611366912739px"
    },
    "AU": {
      "name": "Australia",
      "cx": "214.49884178480744px",
      "cy": "114.83312505007686px"
    },
    "AT": {
      "name": "Austria",
      "cx": "134.73435446094823px",
      "cy": "66.64292374400448px"
    },
    "AZ": {
      "name": "Azerbaijan",
      "cx": "156.83012919665686px",
      "cy": "72.79050444972464px"
    },
    "BS": {
      "name": "Bahamas",
      "cx": "73.21999344891222px",
      "cy": "83.61254647821724px"
    },
    "BH": {
      "name": "Bahrain",
      "cx": "158.82433932493828px",
      "cy": "82.92558096206956px"
    },
    "BD": {
      "name": "Bangladesh",
      "cx": "185.45059802760875px",
      "cy": "84.50172186578165px"
    },
    "BB": {
      "name": "Barbados",
      "cx": "85.16414248187749px",
      "cy": "91.15184940795177px"
    },
    "BY": {
      "name": "Belarus",
      "cx": "143.7015017458863px",
      "cy": "60.7722382408146px"
    },
    "BE": {
      "name": "Belgium",
      "cx": "127.99049664096184px",
      "cy": "63.90746297671655px"
    },
    "BZ": {
      "name": "Belize",
      "cx": "65.7929050755944px",
      "cy": "88.66557285221835px"
    },
    "BJ": {
      "name": "Benin",
      "cx": "126.54934965467632px",
      "cy": "93.53097509158945px"
    },
    "BM": {
      "name": "Bermuda",
      "cx": "81.680329930764px",
      "cy": "78.6306498627972px"
    },
    "BT": {
      "name": "Bhutan",
      "cx": "185.50229354974758px",
      "cy": "81.95213138052829px"
    },
    "BO": {
      "name": "Bolivia, Plurinational State of",
      "cx": "82.4576340243375px",
      "cy": "109.05682979474162px"
    },
    "BQ": {
      "name": "Bonaire, Sint Eustatius and Saba",
      "cx": "79.34674983889892px",
      "cy": "91.77674345775853px"
    },
    "BA": {
      "name": "Bosnia and Herzegowina",
      "cx": "136.82773475801653px",
      "cy": "69.74000399043113px"
    },
    "BW": {
      "name": "Botswana",
      "cx": "141.51477982136515px",
      "cy": "112.89997163982706px"
    },
    "BV": {
      "name": "Bouvet Island",
      "cx": "127.23885679504087px",
      "cy": "138.24148385808212px"
    },
    "BR": {
      "name": "Brazil",
      "cx": "90.26071616662885px",
      "cy": "107.77791927610245px"
    },
    "IO": {
      "name": "British Indian Ocean Territory",
      "cx": "173.08715127767624px",
      "cy": "102.95889641860006px"
    },
    "BN": {
      "name": "Brunei Darussalam",
      "cx": "201.75561993949879px",
      "cy": "96.41779307474448px"
    },
    "BG": {
      "name": "Bulgaria",
      "cx": "142.05064435086425px",
      "cy": "70.7154370705188px"
    },
    "BF": {
      "name": "Burkina Faso",
      "cx": "123.95525604369959px",
      "cy": "91.73990871992014px"
    },
    "BI": {
      "name": "Burundi",
      "cx": "145.01646736873198px",
      "cy": "101.16926143485652px"
    },
    "KH": {
      "name": "Cambodia",
      "cx": "195.2415251992087px",
      "cy": "91.53870424528647px"
    },
    "CM": {
      "name": "Cameroon",
      "cx": "133.26561155260782px",
      "cy": "94.70684545709308px"
    },
    "CA": {
      "name": "Canada",
      "cx": "53.85140604862364px",
      "cy": "58.24294379592774px"
    },
    "CV": {
      "name": "Cape Verde",
      "cx": "109.58447156699454px",
      "cy": "89.0739874149262px"
    },
    "KY": {
      "name": "Cayman Islands",
      "cx": "70.63868288881564px",
      "cy": "87.32324012620712px"
    },
    "CF": {
      "name": "Central African Republic",
      "cx": "139.0090007878432px",
      "cy": "95.16566113207425px"
    },
    "TD": {
      "name": "Chad",
      "cx": "137.5323051854215px",
      "cy": "89.75134336619965px"
    },
    "CL": {
      "name": "Chile",
      "cx": "77.13600184008497px",
      "cy": "122.09451101265898px"
    },
    "CN": {
      "name": "China",
      "cx": "194.70927206389234px",
      "cy": "76.053920029563px"
    },
    "CX": {
      "name": "Christmas Island",
      "cx": "195.70949846177888px",
      "cy": "105.45001104934991px"
    },
    "CC": {
      "name": "Cocos (Keeling) Islands",
      "cx": "189.80904167861988px",
      "cy": "106.50096263927018px"
    },
    "CO": {
      "name": "Colombia",
      "cx": "75.29326487696373px",
      "cy": "96.39635980822516px"
    },
    "KM": {
      "name": "Comoros",
      "cx": "153.99103881840642px",
      "cy": "106.18275769302744px"
    },
    "CG": {
      "name": "Congo",
      "cx": "144.7434283062655px",
      "cy": "99.18222524547708px"
    },
    "CD": {
      "name": "Congo, the Democratic Republic of the",
      "cx": "139.55707902838375px",
      "cy": "101.56947279243508px"
    },
    "CK": {
      "name": "Cook Islands",
      "cx": "18.10482556658346px",
      "cy": "112.19418315343893px"
    },
    "CR": {
      "name": "Costa Rica",
      "cx": "68.96690576709814px",
      "cy": "93.26239615942931px"
    },
    "CI": {
      "name": "Côte d'Ivoire",
      "cx": "121.28886764661803px",
      "cy": "94.60376113090774px"
    },
    "HR": {
      "name": "Croatia",
      "cx": "135.16917227487593px",
      "cy": "68.74320229528941px"
    },
    "CU": {
      "name": "Cuba",
      "cx": "72.96249462104313px",
      "cy": "85.90849594787849px"
    },
    "CW": {
      "name": "Curaçao",
      "cx": "78.84398723608321px",
      "cy": "91.78214225069387px"
    },
    "CY": {
      "name": "Cyprus",
      "cx": "147.36539428021527px",
      "cy": "76.59604132314946px"
    },
    "CZ": {
      "name": "Czech Republic",
      "cx": "135.3517904700941px",
      "cy": "64.55066565609482px"
    },
    "DK": {
      "name": "Denmark",
      "cx": "131.3569268386934px",
      "cy": "58.09883353020673px"
    },
    "DJ": {
      "name": "Djibouti",
      "cx": "153.4939368986808px",
      "cy": "91.99352571740579px"
    },
    "DM": {
      "name": "Dominica",
      "cx": "83.94131424240734px",
      "cy": "89.77573540259354px"
    },
    "DO": {
      "name": "Dominican Republic",
      "cx": "78.05946838243797px",
      "cy": "87.69001882398895px"
    },
    "EC": {
      "name": "Ecuador",
      "cx": "72.69338688026924px",
      "cy": "100.24271007039985px"
    },
    "EG": {
      "name": "Egypt",
      "cx": "145.6076254340631px",
      "cy": "82.42014882858213px"
    },
    "SV": {
      "name": "El Salvador",
      "cx": "65.52604458807362px",
      "cy": "90.78125214162245px"
    },
    "GQ": {
      "name": "Equatorial Guinea",
      "cx": "131.86947318870983px",
      "cy": "98.15208283068507px"
    },
    "ER": {
      "name": "Eritrea",
      "cx": "151.6153556105999px",
      "cy": "89.92237283406966px"
    },
    "EE": {
      "name": "Estonia",
      "cx": "141.73471566726892px",
      "cy": "55.49803231773078px"
    },
    "ET": {
      "name": "Ethiopia",
      "cx": "152.0885827224694px",
      "cy": "93.62991961522275px"
    },
    "FK": {
      "name": "Falkland Islands (Malvinas)",
      "cx": "85.17724532646254px",
      "cy": "135.61586304888013px"
    },
    "FO": {
      "name": "Faroe Islands",
      "cx": "120.3758324671696px",
      "cy": "51.505778882362264px"
    },
    "FJ": {
      "name": "Fiji",
      "cx": "3.281051299727238px",
      "cy": "109.9502743599623px"
    },
    "FI": {
      "name": "Finland",
      "cx": "142.2261435903172px",
      "cy": "51.46565266124337px"
    },
    "FR": {
      "name": "France",
      "cx": "126.48105228988351px",
      "cy": "67.77454027271938px"
    },
    "GF": {
      "name": "French Guiana",
      "cx": "89.45755093149616px",
      "cy": "96.77980213980422px"
    },
    "PF": {
      "name": "French Polynesia",
      "cx": "25.043163452851452px",
      "cy": "109.92908379712685px"
    },
    "TF": {
      "name": "French Southern Territories",
      "cx": "171.3958828634462px",
      "cy": "133.23876828696135px"
    },
    "GA": {
      "name": "Gabon",
      "cx": "132.7670023207121px",
      "cy": "99.62569030238059px"
    },
    "GM": {
      "name": "Gambia",
      "cx": "114.75714210401249px",
      "cy": "90.99805874200966px"
    },
    "GE": {
      "name": "Georgia",
      "cx": "154.0068224441124px",
      "cy": "71.05630722461918px"
    },
    "DE": {
      "name": "Germany",
      "cx": "131.99232682434953px",
      "cy": "63.27839025177996px"
    },
    "GH": {
      "name": "Ghana",
      "cx": "124.31545815867335px",
      "cy": "94.35746605803783px"
    },
    "GI": {
      "name": "Gibraltar",
      "cx": "121.41832053980102px",
      "cy": "75.846824284976px"
    },
    "GR": {
      "name": "Greece",
      "cx": "139.6009991479304px",
      "cy": "73.62341896636909px"
    },
    "GL": {
      "name": "Greenland",
      "cx": "96.49667802581043px",
      "cy": "36.32699272447198px"
    },
    "GD": {
      "name": "Grenada",
      "cx": "83.73523864371076px",
      "cy": "91.81472982562174px"
    },
    "GP": {
      "name": "Guadeloupe",
      "cx": "83.82087377809368px",
      "cy": "89.24532508734977px"
    },
    "GU": {
      "name": "Guam",
      "cx": "221.8705516561835px",
      "cy": "90.9973662182881px"
    },
    "GT": {
      "name": "Guatemala",
      "cx": "64.63341271531885px",
      "cy": "89.54607844425763px"
    },
    "GG": {
      "name": "Guernsey",
      "cx": "123.27038571134965px",
      "cy": "64.87679543042513px"
    },
    "GN": {
      "name": "Guinea",
      "cx": "118.51270704969836px",
      "cy": "93.14256775160243px"
    },
    "GW": {
      "name": "Guinea-Bissau",
      "cx": 0,
      "cy": 0
    },
    "GY": {
      "name": "Guyana",
      "cx": "85.57426653507402px",
      "cy": "96.2219507273527px"
    },
    "HT": {
      "name": "Haiti",
      "cx": "76.639421090435px",
      "cy": "87.54063461312519px"
    },
    "HM": {
      "name": "Heard and McDonald Islands",
      "cx": "174.1760816252692px",
      "cy": "136.88193386520624px"
    },
    "VA": {
      "name": "Holy See (Vatican City State)",
      "cx": "133.33162219170282px",
      "cy": "71.39009114080731px"
    },
    "HN": {
      "name": "Honduras",
      "cx": "67.30205371784938px",
      "cy": "89.90954944410062px"
    },
    "HK": {
      "name": "Hong Kong",
      "cx": "201.34204773409436px",
      "cy": "85.34234169681098px"
    },
    "HU": {
      "name": "Hungary",
      "cx": "138.04818802745893px",
      "cy": "66.95628829474276px"
    },
    "IS": {
      "name": "Iceland",
      "cx": "112.27459561483883px",
      "cy": "47.380834415455936px"
    },
    "IN": {
      "name": "India",
      "cx": "177.82810031299647px",
      "cy": "86.50550404434064px"
    },
    "ID": {
      "name": "Indonesia",
      "cx": "201.21615739630656px",
      "cy": "99.61703660011692px"
    },
    "IR": {
      "name": "Iran, Islamic Republic of",
      "cx": "160.91861745286855px",
      "cy": "78.54528446775247px"
    },
    "IQ": {
      "name": "Iraq",
      "cx": "154.22251526981492px",
      "cy": "77.97714326023704px"
    },
    "IE": {
      "name": "Ireland",
      "cx": "119.85382778346299px",
      "cy": "61.34392715613433px"
    },
    "IM": {
      "name": "Isle of Man",
      "cx": "121.95723916608507px",
      "cy": "60.23506365529385px"
    },
    "IL": {
      "name": "Israel",
      "cx": "148.3165818522023px",
      "cy": "79.5207170064284px"
    },
    "IT": {
      "name": "Italy",
      "cx": "133.4078849620422px",
      "cy": "71.41506930844173px"
    },
    "JM": {
      "name": "Jamaica",
      "cx": "73.2860743355784px",
      "cy": "88.08618535556997px"
    },
    "JP": {
      "name": "Japan",
      "cx": "217.49459160604414px",
      "cy": "75.7991759744341px"
    },
    "JE": {
      "name": "Jersey",
      "cx": "123.57414156129975px",
      "cy": "65.10828235596225px"
    },
    "JO": {
      "name": "Jordan",
      "cx": "149.24438634933142px",
      "cy": "79.84287549954443px"
    },
    "KZ": {
      "name": "Kazakhstan",
      "cx": "169.77358338078946px",
      "cy": "66.1933648517164px"
    },
    "KE": {
      "name": "Kenya",
      "cx": "150.36017133984734px",
      "cy": "99.15735476079129px"
    },
    "KI": {
      "name": "Kiribati",
      "cx": "12.112796056716157px",
      "cy": "101.16767447007705px"
    },
    "KP": {
      "name": "Korea, Democratic People's Republic of",
      "cx": "210.30737460340228px",
      "cy": "72.63577513810384px"
    },
    "KR": {
      "name": "Korea, Republic of",
      "cx": "210.4791994934682px",
      "cy": "76.01976482202686px"
    },
    "KW": {
      "name": "Kuwait",
      "cx": "156.7664641574145px",
      "cy": "80.72521251056347px"
    },
    "KG": {
      "name": "Kyrgyzstan",
      "cx": "175.02035038685676px",
      "cy": "71.95046148267475px"
    },
    "LA": {
      "name": "Lao People's Democratic Republic",
      "cx": "193.57199666879328px",
      "cy": "86.97727294850668px"
    },
    "LV": {
      "name": "Latvia",
      "cx": "141.4601359083104px",
      "cy": "57.4278675565565px"
    },
    "LB": {
      "name": "Lebanon",
      "cx": "148.99274683792262px",
      "cy": "77.52229914560485px"
    },
    "LS": {
      "name": "Lesotho",
      "cx": "143.88897511871173px",
      "cy": "117.76689804025771px"
    },
    "LR": {
      "name": "Liberia",
      "cx": "118.69143426540043px",
      "cy": "95.2762667740811px"
    },
    "LY": {
      "name": "Libya",
      "cx": "136.52617531545843px",
      "cy": "82.74600656958704px"
    },
    "LI": {
      "name": "Liechtenstein",
      "cx": "131.3927785229224px",
      "cy": "66.95319270812962px"
    },
    "LT": {
      "name": "Lithuania",
      "cx": "140.97715776453762px",
      "cy": "59.26534900801881px"
    },
    "LU": {
      "name": "Luxembourg",
      "cx": "129.10084112434652px",
      "cy": "64.55273014226178px"
    },
    "MO": {
      "name": "Macao",
      "cx": "200.9636314274521px",
      "cy": "85.47060351408297px"
    },
    "MK": {
      "name": "North Macedonia",
      "cx": "139.5481214595224px",
      "cy": "71.62690427007917px"
    },
    "MG": {
      "name": "Madagascar",
      "cx": "156.3565802840089px",
      "cy": "110.61621904124104px"
    },
    "MW": {
      "name": "Malawi",
      "cx": "147.94856017902023px",
      "cy": "107.17183405073784px"
    },
    "MY": {
      "name": "Malaysia",
      "cx": "193.22428457197418px",
      "cy": "96.61333901787776px"
    },
    "MV": {
      "name": "Maldives",
      "cx": "173.9864279016395px",
      "cy": "97.21955082201421px"
    },
    "ML": {
      "name": "Mali",
      "cx": "122.32646709041785px",
      "cy": "88.4260285207935px"
    },
    "MT": {
      "name": "Malta",
      "cx": "134.6175053200827px",
      "cy": "75.99771938595632px"
    },
    "MH": {
      "name": "Marshall Islands",
      "cx": "239.52660763908216px",
      "cy": "94.85102106471916px"
    },
    "MQ": {
      "name": "Martinique",
      "cx": "84.1733332726751px",
      "cy": "90.25650495643785px"
    },
    "MR": {
      "name": "Mauritania",
      "cx": "117.68031347276161px",
      "cy": "86.23951441507393px"
    },
    "MU": {
      "name": "Mauritius",
      "cx": "163.50379898864904px",
      "cy": "111.62375052919424px"
    },
    "YT": {
      "name": "Mayotte",
      "cx": "155.21732323837824px",
      "cy": "106.90883335763284px"
    },
    "MX": {
      "name": "Mexico",
      "cx": "56.38967625637448px",
      "cy": "84.53481496048369px"
    },
    "FM": {
      "name": "Micronesia, Federated States of",
      "cx": "225.72217982093687px",
      "cy": "94.67304763930738px"
    },
    "MD": {
      "name": "Moldova, Republic of",
      "cx": "143.98014776877662px",
      "cy": "66.73580451994958px"
    },
    "MC": {
      "name": "Monaco",
      "cx": "129.96724977948952px",
      "cy": "69.88767712794579px"
    },
    "MN": {
      "name": "Mongolia",
      "cx": "194.47595579514362px",
      "cy": "67.22041585520228px"
    },
    "ME": {
      "name": "Montenegro",
      "cx": "137.96194133779204px",
      "cy": "70.73603265906719px"
    },
    "MS": {
      "name": "Montserrat",
      "cx": "83.3951293411661px",
      "cy": "88.94636035816983px"
    },
    "MA": {
      "name": "Morocco",
      "cx": "120.25486354041335px",
      "cy": "78.99618206912251px"
    },
    "MZ": {
      "name": "Mozambique",
      "cx": "148.77014700341252px",
      "cy": "110.55204274668425px"
    },
    "MM": {
      "name": "Myanmar",
      "cx": "189.19689631512017px",
      "cy": "85.65359961260634px"
    },
    "NA": {
      "name": "Namibia",
      "cx": "137.3705370714497px",
      "cy": "113.30921255580247px"
    },
    "NR": {
      "name": "Nauru",
      "cx": "236.6812632199239px",
      "cy": "99.45704519573886px"
    },
    "NP": {
      "name": "Nepal",
      "cx": "181.2810213274302px",
      "cy": "81.35359632785786px"
    },
    "NL": {
      "name": "Netherlands",
      "cx": "128.53998652317068px",
      "cy": "62.34282701878489px"
    },
    "NC": {
      "name": "New Caledonia",
      "cx": "235.8025256477228px",
      "cy": "111.98032214944979px"
    },
    "NZ": {
      "name": "New Zealand",
      "cx": "1.1541816438948878px",
      "cy": "126.0941029049136px"
    },
    "NI": {
      "name": "Nicaragua",
      "cx": "67.99427729833998px",
      "cy": "91.35424505807643px"
    },
    "NE": {
      "name": "Niger",
      "cx": "130.40683245271873px",
      "cy": "88.40266659602096px"
    },
    "NG": {
      "name": "Nigeria",
      "cx": "130.80397274768896px",
      "cy": "93.66822326331504px"
    },
    "NU": {
      "name": "Niue",
      "cx": "11.35466152177916px",
      "cy": "110.79865410022607px"
    },
    "NF": {
      "name": "Norfolk Island",
      "cx": "237.3658150966178px",
      "cy": "117.37500768880192px"
    },
    "MP": {
      "name": "Northern Mariana Islands",
      "cx": "222.45940627020457px",
      "cy": "89.97304704772174px"
    },
    "NO": {
      "name": "Norway",
      "cx": "130.66593225618604px",
      "cy": "53.27534466503174px"
    },
    "OM": {
      "name": "Oman",
      "cx": "162.44892198746302px",
      "cy": "85.93960986431092px"
    },
    "PK": {
      "name": "Pakistan",
      "cx": "171.39358068328272px",
      "cy": "79.98904669368305px"
    },
    "PW": {
      "name": "Palau",
      "cx": "215.03900145151547px",
      "cy": "94.61890478594226px"
    },
    "PS": {
      "name": "Palestinian Territory, Occupied",
      "cx": "undefinedpx",
      "cy": "undefinedpx"
    },
    "PA": {
      "name": "Panama",
      "cx": "70.95477889543523px",
      "cy": "93.99870301246912px"
    },
    "PG": {
      "name": "Papua New Guinea",
      "cx": "221.309788042338px",
      "cy": "102.94186326521596px"
    },
    "PY": {
      "name": "Paraguay",
      "cx": "85.89964525645583px",
      "cy": "113.62588994110904px"
    },
    "PE": {
      "name": "Peru",
      "cx": "74.81302638846667px",
      "cy": "104.68384790132271px"
    },
    "PH": {
      "name": "Philippines",
      "cx": "206.46979929800594px",
      "cy": "91.34543624226853px"
    },
    "PN": {
      "name": "Pitcairn",
      "cx": "39.14794272294037px",
      "cy": "114.23938504526161px"
    },
    "PL": {
      "name": "Poland",
      "cx": "137.8085647979653px",
      "cy": "62.55082747374607px"
    },
    "PT": {
      "name": "Portugal",
      "cx": "119.49763887877916px",
      "cy": "73.37100980051787px"
    },
    "PR": {
      "name": "Puerto Rico",
      "cx": "80.44955970160437px",
      "cy": "88.01589658812249px"
    },
    "QA": {
      "name": "Qatar",
      "cx": "159.24327175453547px",
      "cy": "83.39988851868051px"
    },
    "RE": {
      "name": "Réunion",
      "cx": "162.15520083579892px",
      "cy": "112.11590261212015px"
    },
    "RO": {
      "name": "Romania",
      "cx": "141.70337381020687px",
      "cy": "68.0207606985394px"
    },
    "RU": {
      "name": "Russian Federation",
      "cx": "195.46082674299612px",
      "cy": "51.97267922915278px"
    },
    "RW": {
      "name": "Rwanda",
      "cx": "144.9863626048495px",
      "cy": "100.30820273467187px"
    },
    "BL": {
      "name": "Saint Barthélemy",
      "cx": "82.96296160013529px",
      "cy": "88.21847722152745px"
    },
    "SH": {
      "name": "Saint Helena, Ascension and Tristan da Cunha",
      "cx": "118.28921975420577px",
      "cy": "114.085784599121px"
    },
    "KN": {
      "name": "Saint Kitts and Nevis",
      "cx": "82.99663694770689px",
      "cy": "88.55999168404684px"
    },
    "LC": {
      "name": "Saint Lucia",
      "cx": "84.20362737048758px",
      "cy": "90.70998854002087px"
    },
    "MF": {
      "name": "Saint Martin",
      "cx": "82.81795194745px",
      "cy": "88.11065778382684px"
    },
    "PM": {
      "name": "Saint Pierre and Miquelon",
      "cx": "87.32328387190697px",
      "cy": "67.20047832458464px"
    },
    "VC": {
      "name": "Saint Vincent and the Grenadines",
      "cx": "83.99734370517534px",
      "cy": "91.2810195832452px"
    },
    "WS": {
      "name": "Samoa",
      "cx": "9.857788809842914px",
      "cy": "107.48344235990508px"
    },
    "SM": {
      "name": "San Marino",
      "cx": "133.33455787115335px",
      "cy": "69.71793701617334px"
    },
    "ST": {
      "name": "Sao Tome and Principe",
      "cx": "129.42431312593928px",
      "cy": "99.03133812064648px"
    },
    "SA": {
      "name": "Saudi Arabia",
      "cx": "155.15906324796936px",
      "cy": "84.36989347599938px"
    },
    "SN": {
      "name": "Senegal",
      "cx": "115.33101592171236px",
      "cy": "90.3459004986354px"
    },
    "RS": {
      "name": "Serbia",
      "cx": "139.05343404917167px",
      "cy": "69.65606894122321px"
    },
    "SC": {
      "name": "Seychelles",
      "cx": "162.12549146538842px",
      "cy": "101.95553481129049px"
    },
    "SL": {
      "name": "Sierra Leone",
      "cx": "117.11896580053863px",
      "cy": "94.04569872485612px"
    },
    "SG": {
      "name": "Singapore",
      "cx": "194.45801255839245px",
      "cy": "98.33146797957907px"
    },
    "SX": {
      "name": "Sint Maarten",
      "cx": "82.81477468325703px",
      "cy": "88.12855802625147px"
    },
    "SK": {
      "name": "Slovakia",
      "cx": "138.17912943322386px",
      "cy": "65.60677235107995px"
    },
    "SI": {
      "name": "Slovenia",
      "cx": "135.03233194640094px",
      "cy": "67.84078822761786px"
    },
    "SB": {
      "name": "Solomon Islands",
      "cx": "232.14841558944806px",
      "cy": "104.96117367315289px"
    },
    "SO": {
      "name": "Somalia",
      "cx": "155.90867441093732px",
      "cy": "96.04614968590997px"
    },
    "ZA": {
      "name": "South Africa",
      "cx": "140.34575319312006px",
      "cy": "118.4256422621803px"
    },
    "GS": {
      "name": "South Georgia and the South Sandwich Islands",
      "cx": "100.5217929374564px",
      "cy": "138.25055146825838px"
    },
    "SS": {
      "name": "South Sudan",
      "cx": "145.94513544266943px",
      "cy": "95.00493796369447px"
    },
    "ES": {
      "name": "Spain",
      "cx": "122.4916800114751px",
      "cy": "72.5381709323998px"
    },
    "LK": {
      "name": "Sri Lanka",
      "cx": "179.03831008161026px",
      "cy": "94.40199628873926px"
    },
    "SD": {
      "name": "Sudan",
      "cx": "145.2163384343328px",
      "cy": "91.35585158904047px"
    },
    "SR": {
      "name": "Suriname",
      "cx": "87.51604120013359px",
      "cy": "96.78857765828396px"
    },
    "SJ": {
      "name": "Svalbard and Jan Mayen",
      "cx": "140.83599152372736px",
      "cy": "22.9234117633583px"
    },
    "SZ": {
      "name": "Swaziland",
      "cx": "146.05143487019856px",
      "cy": "115.66604729082985px"
    },
    "SE": {
      "name": "Sweden",
      "cx": "137.47295869924514px",
      "cy": "53.691983137912274px"
    },
    "CH": {
      "name": "Switzerland",
      "cx": "130.50440699810315px",
      "cy": "67.25930068210232px"
    },
    "SY": {
      "name": "Syrian Arab Republic",
      "cx": "151.0898241643081px",
      "cy": "76.83362701351734px"
    },
    "TW": {
      "name": "Taiwan, Province of China",
      "cx": "205.75928545776262px",
      "cy": "85.19979280604345px"
    },
    "TJ": {
      "name": "Tajikistan",
      "cx": "172.68545158082455px",
      "cy": "73.78801109240992px"
    },
    "TZ": {
      "name": "Tanzania, United Republic of",
      "cx": "148.34147625337724px",
      "cy": "102.97450074285524px"
    },
    "TH": {
      "name": "Thailand",
      "cx": "192.56648297038313px",
      "cy": "89.49206750431694px"
    },
    "TL": {
      "name": "Timor-Leste",
      "cx": "209.11480232734885px",
      "cy": "104.49192001827365px"
    },
    "TG": {
      "name": "Togo",
      "cx": "125.55179935473927px",
      "cy": "93.94918653336532px"
    },
    "TK": {
      "name": "Tokelau",
      "cx": "10.029212285216403px",
      "cy": "104.690070836823px"
    },
    "TO": {
      "name": "Tonga",
      "cx": "248.63690141041064px",
      "cy": "112.15699678022986px"
    },
    "TT": {
      "name": "Trinidad and Tobago",
      "cx": "84.04064631512016px",
      "cy": "92.68723937610538px"
    },
    "TN": {
      "name": "Tunisia",
      "cx": "131.38082037923522px",
      "cy": "77.49902121737502px"
    },
    "TR": {
      "name": "Turkey",
      "cx": "148.57864543414874px",
      "cy": "73.70876952725226px"
    },
    "TM": {
      "name": "Turkmenistan",
      "cx": "164.8446083584173px",
      "cy": "73.70415705240582px"
    },
    "TC": {
      "name": "Turks and Caicos Islands",
      "cx": "76.96542802581043px",
      "cy": "85.79726505575469px"
    },
    "TV": {
      "name": "Tuvalu",
      "cx": "3.002936482279929px",
      "cy": "103.42213049622077px"
    },
    "UG": {
      "name": "Uganda",
      "cx": "146.60298467880406px",
      "cy": "98.31870778645023px"
    },
    "UA": {
      "name": "Ukraine",
      "cx": "145.8505361160827px",
      "cy": "65.86925963813415px"
    },
    "AE": {
      "name": "United Arab Emirates",
      "cx": "161.02550883326407px",
      "cy": "84.67259044584783px"
    },
    "GB": {
      "name": "United Kingdom",
      "cx": "122.70124992506926px",
      "cy": "59.0455097550562px"
    },
    "US": {
      "name": "United States",
      "cx": "60.96573216434238px",
      "cy": "75.13668048781895px"
    },
    "UM": {
      "name": "United States Minor Outlying Islands",
      "cx": "236.49095519034543px",
      "cy": "87.34294537289645px"
    },
    "UY": {
      "name": "Uruguay",
      "cx": "87.69129082655029px",
      "cy": "119.80864852318412px"
    },
    "UZ": {
      "name": "Uzbekistan",
      "cx": "168.20912180099253px",
      "cy": "71.81215340525266px"
    },
    "VU": {
      "name": "Vanuatu",
      "cx": "236.699765092122px",
      "cy": "108.48684542803178px"
    },
    "VE": {
      "name": "Venezuela, Bolivarian Republic of",
      "cx": "80.44984002286456px",
      "cy": "95.27886744689101px"
    },
    "VN": {
      "name": "Viet Nam",
      "cx": "197.44009755447462px",
      "cy": "90.61788477021221px"
    },
    "VG": {
      "name": "Virgin Islands, British",
      "cx": "81.7542785454016px",
      "cy": "87.889511113158px"
    },
    "VI": {
      "name": "Virgin Islands, U.S.",
      "cx": "81.58276274464886px",
      "cy": "87.94323560464042px"
    },
    "WF": {
      "name": "Wallis And Futuna Islands",
      "cx": "246.68451534795696px",
      "cy": "107.81433791850175px"
    },
    "EH": {
      "name": "Western Sahara",
      "cx": "116.37906151385795px",
      "cy": "84.15323035213233px"
    },
    "YE": {
      "name": "Yemen",
      "cx": "157.4586516105828px",
      "cy": "89.68994209555984px"
    },
    "ZM": {
      "name": "Zambia",
      "cx": "143.63188506480438px",
      "cy": "107.09759026966753px"
    },
    "ZW": {
      "name": "Zimbabwe",
      "cx": "144.50531325867377px",
      "cy": "110.77388334500354px"
    }
  };

});