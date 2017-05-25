## 0.1.4 - (2017-05-25)

_Дополнения_:

  - Доработка виджета `GridView`:
    - Добавлена форма `Настройки` грида (видимость, порядок колонок. Количество записей на страницу.)

_Исправления_:

  - Исправлено добавление сортировки в методе `applyPopularityOrder` класса хелпера `CardListHelper`

## 0.1.3 - (2017-05-24)

_Исправления_:

  - Рефакторинг виджета `GridView`:
    - Рефакторинг плагина `gridselected2storage.js`
      - При применении фильтра, выделенные записи очищаются
    - Рефакторинг плагина `gridselected2textinput.js`

## 0.1.2 - (2017-05-23)

_Дополнения_:

  - Доработка виджета `GridView`:
    - Создан плагин `gridselected2storage.js`, сохраняющий выбранные строки в localStorage
    - Создан плагин `gridselected2textinput.js`, сохраняющий выбранные строки в Input элемент
    - Создан плагин - шаблонная заготовка `plugin.js`

  - Сервисный слой:
    - Доработка слоя создания ролей

_Исправления_:

  - Рефакторинг виджета `GridView`
  
## 0.1.1 - (2017-05-22)

_Дополнения_:

  - Доработка виджета `GridView`:
    - Создан плагин `wkgridview.js`
    - Добавление возможности сохранения в `Localstorage` выделенных записей грида
    - Настройка виджета
  - Сервисный слой:
    - Добавлены сервисы:
      - `RoleService`
    - Добавлены репозитории:
      - `RoleRepository`
      - `AuthItemChildRepository`
    - Добавлены формы:
      - `RoleForm`

_Исправления_:

  - Исправлено назначение вида авторизационных единиц в действии `Init` контроллера `RbacController`

## 0.1.0 - (2017-05-21)

_Дополнения_:

  - Доработка виджета `CardList`:
      - Добавлен метод `applyPopularityOrder` класса хелпера `CardListHelper`, позволяющий добавить сортировку по популярности к модели поиска ActiveRecord
        ```php
        public function search($params)
            {
                $query = Post::find();

                // add conditions that should always apply here

                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                ]);

                $this->load($params);

                ...

                $query->andFilterWhere(['like', 'name', $this->name])
                    ->andFilterWhere(['like', 'description', $this->description])

                // Метод applyPopularityOrder применяет сортировку к ActiveQuery $query по популярности по атрибуту 'name' (PopularityID)
                CardListHelper::applyPopularityOrder($query, 'name');

                return $dataProvider;
            }
        ```

_Исправления_:

  - Проведен рефакторинг плагина `wkcardlist.js`

## 0.0.9 - (2017-05-20)

_Дополнения_:

  - Доработка виджета `CardList`:
      - Добавлено свойство `popularity`, добавляет возможность выводить сверху часто используемые карты, по умолчанию false
      - Добавлен параметр `popularityID` в метод `createAjaxCards` класса хелпера `CardListHelper`, позволяющий присвоить ИД карте из базы данных, для сортировки карт по популярности

_Исправления_:

  - Проведен рефакторинг плагина `wkcardlist.js`

## 0.0.8 - (2017-05-19)

_Дополнения_:

  - Доработка виджета `CardList`:
      - Доработан поиск карт, включая поиск с помощью ajax
      - Добавлены свойства:
        - `cardsPerPage` - Количество карт, выгружаемых при ajax запросе, по умолчанию `6`
        - `language` - Язык виджета, по умолчанию `ru`
        - `search` - Добавить поиск по картам, по умолчанию `false`

        _для активации поиска только у карт в `items` необходимо указать массив с ключами:_
        ```php
            [
                'search' => true
            ]
        ```

        _для активации поиска у карт в `items` и ajax необходимо указать массив с ключами:_
        ```php
            [
                'search' => [
                    /** @var ActiveRecord 'modelSearch' - Модель поиска карт */
                    'modelSearch' => $modelSearch,
                    /** @var string 'searchAttributeName' - Имя атрибута модели поиска, по которому будет осуществляться поиск */
                    'searchAttributeName' => 'modelAttribute',
                ]
            ]
        ```

      - Добавлен класс хелпер `CardListHelper` с методом `createAjaxCards`, позволяющий создавать карты на основе провайдера данных `ActiveDataProvider`

          ```php
            public function actionAjaxLoadingCards()
            {
                Yii::$app->response->format = Response::FORMAT_JSON;

                $searchModel = new CardsSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return CardListHelper::createAjaxCards($dataProvider, 'title', 'link', 'preview', 'description', 'styleCard');
            }
          ```

## 0.0.7 - (2017-05-18)

_Дополнения_:

  - Доработка виджета `CardList`:
      - Доработан вывод карт через свойство `items` и вывод с помощью ajax
  - Добавлены методы `createPersmission` и `createRole` в класс хелпер `RbacHelper`
  - Добавлен виджет `GridView`, расширенный от `kartik-v/yii2-grid`

_Исправления_:

  - Изменен вид авторизационной единицы `Administrator` с системной на пользовательскую

## 0.0.6 - (2017-05-17)

_Дополнения_:

  - Доработка виджета `CardList`
  - Локализация виджета `CardList`
  - Создание генераторов и шаблонов `gii` для моделей и crud
  - Добавлено поле `view` (Вид роли: 0 - Системная, 1 - Пользовательская) в модель `AuthItem`

## 0.0.5 - (2017-05-16)

_Дополнения_:

  - Доработка виджета `CardList`

## 0.0.4 - (2017-05-15)

_Дополнения_:

  - Доработка виджета `CardList`

## 0.0.3 - (2017-05-14)

_Дополнения_:

  - Сервисный слой:
    - Добавлены системные классы:
      - `BaseService`
      - `proxyService`
    - Добавлены системные интерфейсы:
      - `RepositoryInterface`
    - Добавлены системные исключения:
      - `ServiceErrorsException`
    - Добавлены сервисы:
      - `PersonService`
    - Добавлены репозитории:
      - `PersonRepository`
    - Добавлены формы:
      - `LoginForm`
    - Добавлены модели:
      - `Person`
  - Добавлен виджет `CardList`

_Исправления_:

  - Авторизация перенесена в сервисный слой

## 0.0.2 - (2017-05-13)

_Дополнения_:

  - Настроен `i18n`
  - Добавлен `CHANGELOG.md`
  - Настроена `RBAC` авторизация
  - Доработана миграция `m130524_201442_init`
  - Доработана авторизация в системе `/manager/login`
  - Добавлены алиасы:
    - `@i18n`: `yii message @i18n`, эквивалентно `yii message common/config/i18n.php`
    - `@domain`: путь к папке `domain` приложения

_Исправления_:

  - Доработана верстка. Теперь верстка не прыгает при появлении скролинга или открытии модального окна
  - Переименован URL бэкенда на `/manager`