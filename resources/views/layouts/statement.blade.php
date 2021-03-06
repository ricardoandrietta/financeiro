@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="row">
                <div class="col-md-4">
                    <h3>{{trans('app.labels.monthly')}}&nbsp;
                        <span data-toggle="modal" data-target="#modalNewTransaction" data-transaction_type="debit"
                              data-modal_title="{{trans('transaction.labels.new_debit')}}">
                            <span style="color: red" data-toggle="tooltip" data-placement="top"
                                  title="{{trans('transaction.labels.new_debit')}}"
                                  class="glyphicon glyphicon-plus small cursor-pointer"></span>
                        </span>
                        <span data-toggle="modal" data-target="#modalNewTransaction" data-transaction_type="credit"
                              data-modal_title="{{trans('transaction.labels.new_credit')}}">
                            <span style="color: #2ca02c" data-toggle="tooltip" data-placement="top"
                                  title="{{trans('transaction.labels.new_credit')}}"
                                  class="glyphicon glyphicon-plus small cursor-pointer"></span>
                        </span>
                        <span data-toggle="modal" data-target="#modalImportStatement">&nbsp;
                            <span data-toggle="tooltip" data-placement="top"
                                  title="{{trans('transaction.labels.import')}}"
                                  class="glyphicon glyphicon-import small cursor-pointer"></span>
                        </span>
                    </h3>
                </div>
                <div class="col-md-8">
                    <h3>Select a month</h3>
                    <form class="form" action="{{route('statement.monthly')}}" method="post">
                        {{csrf_field()}}
                        <div class="form-group">
                            <ul class="nav navbar-nav">
                                <li class="dropdown">
                                    <input type="month" class="form-control" name="yearMonth" id="yearMonth">
                                </li>
                                <li>
                                    <button class="btn btn-primary" type="submit">Consultar</button>
                                </li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <h4>{{trans('app.labels.listed_month')}}: {{$statementDate}}</h4>
                <table class="table table-responsive table-hover">
                    <thead>
                    <tr>
                        <th style="width: 50%">&nbsp;</th>
                        <th style="width: 25%">{{trans('provision.labels.provisioned')}}</th>
                        <th style="width: 25%">{{trans('provision.labels.posted')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>{{trans('app.labels.balance')}}</th>
                        <th class="{{($totalCreditProvision-$totalDebitProvision >= 0)?'success text-green':'danger  text-red'}}">
                            <span class="glyphicon glyphicon-thumbs-{{($totalCreditProvision-$totalDebitProvision >= 0)?'up':'down'}}"></span> {{Number::formatCurrency($totalCreditProvision-$totalDebitProvision)}}
                        </th>
                        <th class="{{($totalCredit-$totalDebit >= 0)?'success text-green':'danger text-red'}}"><span
                                    class="glyphicon glyphicon-thumbs-{{($totalCredit-$totalDebit >= 0)?'up':'down'}}"></span> {{Number::formatCurrency($totalCredit-$totalDebit)}}
                        </th>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    <tr class="success">
                        <th><span id="credit"
                                  class="glyphicon cursor-pointer glyphicon-triangle-top">&nbsp;</span>{{trans('transaction.credit')}}
                        </th>
                        <th>{{Number::formatCurrency($totalCreditProvision)}}</th>
                        <th>{{Number::formatCurrency($totalCredit)}}</th>
                    </tr>
                    @foreach($statementCredit as $creditItem)
                        <tr class="credit-rows">
                            <td>
                                 <span data-month_to_add="{{$yearMonth}}" data-category="{{$creditItem->category}}"
                                       data-category_id="{{$creditItem->category_id}}" data-toggle="modal"
                                       data-target="#modalDetails" data-waiting_text="{{trans('app.labels.loading')}}">
                                    <span data-toggle="tooltip" data-placement="top"
                                          title="{{trans('app.labels.details')}}" class="glyphicon glyphicon-eye-open"
                                          style="cursor:pointer;">&nbsp;</span>
                                </span>
                                <a data-toggle="tooltip" data-placement="right"
                                   title="{{trans('category.labels.edit')}}"
                                   href="{{route('category.edit', ['id' => $creditItem->category_id])}}">{{$creditItem->category}}</a>
                            </td>
                            <td>{{Number::formatCurrency($creditItem->provisioned_value)}}</td>
                            <td>{{Number::formatCurrency($creditItem->posted_value)}}</td>
                        </tr>
                    @endforeach

                    <tr class="danger">
                        <th><span id="debit"
                                  class="glyphicon cursor-pointer glyphicon-triangle-top">&nbsp;</span>{{trans('transaction.debit')}}
                        </th>
                        <th>{{Number::formatCurrency($totalDebitProvision)}}</th>
                        <th>{{Number::formatCurrency($totalDebit)}}</th>
                    </tr>
                    @foreach($statementDebit as $debitItem)
                        <tr class="debit-rows">
                            <td>
                                <span data-month_to_add="{{$yearMonth}}" data-category="{{$debitItem->category}}"
                                      data-category_id="{{$debitItem->category_id}}" data-toggle="modal"
                                      data-target="#modalDetails" data-waiting_text="{{trans('app.labels.loading')}}">
                                    <span data-toggle="tooltip" data-placement="top"
                                          title="{{trans('app.labels.details')}}"
                                          class="glyphicon glyphicon-eye-open cursor-pointer">&nbsp;</span>
                                </span>
                                <a data-toggle="tooltip" data-placement="right"
                                   title="{{trans('category.labels.edit')}}"
                                   href="{{route('category.edit', ['id' => $debitItem->category_id])}}">{{$debitItem->category}}</a>
                            </td>
                            <td>{{Number::formatCurrency($debitItem->provisioned_value)}}</td>
                            <td class="{{($debitItem->posted_value > $debitItem->provisioned_value)?'btn-danger':''}}">{{Number::formatCurrency($debitItem->posted_value)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>
        <div class="col-md-5">
            <div id="MyChart"></div>
        </div>
    </div>

    <!-- Modal Import Statement Files -->
    <div class="modal fade" id="modalImportStatement" tabindex="-1" role="dialog"
         aria-labelledby="modalImportStatementLabel">
        <div class="modal-dialog" role="document">
            <form id="import_file_form" method="post" enctype="multipart/form-data" action="{{route('import.upload')}}">
                {{csrf_field()}}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalImportStatementLabel">{{trans('app.labels.import_file')}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">

                            <label for="description">{{trans('app.labels.select_ofx_vcs')}}</label>
                            <div class="input-group">
                                <label class="input-group-btn">
                            <span class="btn btn-primary">
                                {{trans('app.labels.browse_file')}}&hellip; <input type="file" name="import_file"
                                                                                   id="import_file"
                                                                                   style="display: none;">
                            </span>
                                </label>
                                <input type="text" id="import_file_name" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="panel panel-warning">
                            <div class="panel-heading panel-warning">
                                These options are just for CSV Files<br>
                                <!-- TODO: Add checkbox to ignore first line (header one) just if de user want it -->
                                <span class="glyphicon glyphicon-info-sign">&nbsp;</span>{{trans('app.labels.header_line_ignored')}}!
                            </div>
                            <div class="panel-body">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="delimiter">Delimiter</label>
                                        <select class="form-control" name="delimiter" id="delimiter">
                                            <option value=";">Semicolon ;</option>
                                            <option value=",">Comma ,</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="date_format">Date Format</label>
                                        <select class="form-control" name="date_format" id="date_format">
                                            <option value="d/m/Y">d/m/Y - {{date('31/08/Y')}}</option>
                                            <option value="m/d/Y">m/d/Y - {{date('08/31/Y')}}</option>
                                            <option value="Y-m-d">Y-m-d - {{date('Y-08-31')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <p>File Columns: Date, Description, Value</p>
                                <label for="description">{{trans('app.labels.check_out_csv_model')}}: </label>
                                <a href="{{asset('files/Exemplo_Importacao_Transacoes.csv')}}">{{trans('app.labels.download')}}</a>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success disableOnClick" data-form_id="test123"
                                id="import_file_button">{{trans('app.labels.import')}}</button>
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">{{trans('app.labels.close')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Category Details -->
    <div class="modal fade" id="modalDetails" tabindex="-1" role="dialog" aria-labelledby="modalDetailsLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
                    <p>{{trans('app.labels.loading')}}...</p>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="50"
                             aria-valuemin="0" aria-valuemax="100" style="width: 50%">
                            <span class="sr-only">50% Complete</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal New Transaction -->
    <div class="modal fade" id="modalNewTransaction" tabindex="-1" role="dialog" aria-labelledby="modalNewTransaction">
        <div class="modal-dialog" role="document">
            <form method="post" action="{{route('transaction.store')}}">
                {{ csrf_field() }}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">{{trans('transaction.labels.new')}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">{{trans('app.labels.description')}}</label>
                                    <input type="text" class="form-control" name="description" id="description"
                                           placeholder="{{trans('app.labels.description')}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div id="divValue" class="form-group">
                                    <label for="transaction_value">{{trans('app.labels.value')}}</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="number" step="0.01" min="0.01" class="form-control"
                                               name="transaction_value" id="transaction_value"
                                               placeholder="{{trans('app.labels.value')}}">

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="transaction_date">{{trans('app.labels.date')}}</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" name="transaction_date"
                                               id="transaction_date" value="{{date('Y-m-d')}}">
                                        <span class="input-group-addon "><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="category">{{trans_choice('category.labels.category', 1)}}</label>
                                    <select name="category" class="form-control">
                                        <option value="invalid_option">{{trans('app.labels.select')}}</option>
                                        @foreach($categories as $categoryId => $categoryName)
                                            <option value="{{$categoryId}}">{{$categoryName}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="transactionType" id="transactionType" value=""
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{trans('app.labels.save')}}</button>
                        <button type="button" class="btn btn-danger"
                                data-dismiss="modal">{{trans('app.labels.close')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')

    <script type="text/javascript" src="{{asset('js/bootstrap/modal.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/Highcharts-5.0.6/code/highcharts.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/Highcharts-5.0.6/code/highcharts-3d.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            //Upload Styled Input
            $(document).on('change', ':file', function () {
                var input = $(this);
                var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                $('#import_file_name').val(label);
            });

            $('#modalImportStatement').modal({'backdrop': 'static', 'show': false});

             $('#import_file_button').click(function () {
            //     $(this).attr('disabled', 'disabled');
                 $('#import_file_form').submit();
             });

            //Show/Hide details
            $('#credit, #debit').on('click', function () {
                var type = $(this)[0].id;
                if ($(this).attr('class') == 'glyphicon cursor-pointer glyphicon-triangle-top') {
                    $('.' + type + '-rows').hide();
                    $(this).removeClass('glyphicon-triangle-top');
                    $(this).addClass('glyphicon-triangle-bottom');
                } else {
                    $('.' + type + '-rows').show();
                    $(this).removeClass('glyphicon-triangle-bottom');
                    $(this).addClass('glyphicon-triangle-top');
                }
            });

            //Modal for New Transaction
            $('#modalNewTransaction').on('show.bs.modal', function (event) {
                var span = $(event.relatedTarget); // Span that triggered the modal
                var transaction_type = span.data('transaction_type'); // Extract info from data-* attributes
                var title = span.data('modal_title'); // Extract info from data-* attributes
                var modal = $(this);

                var valueClass = 'has-success';
                if (transaction_type == 'debit') {
                    valueClass = 'has-error';
                }

                modal.find('#divValue').addClass(valueClass);
                modal.find('#transactionType').val(transaction_type);
                modal.find('#myModalLabel').html(title + ' ' + transaction_type);
            });

            //Modal for Transaction Details of a selected Category
            $('#modalDetails').on('show.bs.modal', function (event) {
                var span = $(event.relatedTarget); // Span that triggered the modal
                var category = span.data('category'); // Extract info from data-* attributes
                var category_id = span.data('category_id'); // Extract info from data-* attributes
                var monthToAdd = span.data('month_to_add'); // Extract info from data-* attributes
                var url_details = '{{route('statement.category.details', ['categoryID' => ''])}}';
                var loadingText = span.data('waiting_text');

                var modal = $(this);
                modal.find('.modal-title').text('{{trans('category.labels.details_of')}} "' + category + '"');

                var waitingBody = '<p>' + loadingText + '...</p>\n' +
                    '                    <div class="progress">\n' +
                    '                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%">\n' +
                    '                            <span class="sr-only">50% Complete</span>\n' +
                    '                        </div>\n' +
                    '                    </div>';

                modal.find('.modal-body').html(waitingBody);

                $.ajax({
                    url: url_details + '/' + category_id + '/' + monthToAdd,
                    success: function (tableDetails) {
                        modal.find('.modal-body').html(tableDetails);
                    }
                });
            }).modal({'backdrop': 'static', 'show': false});

            var myChart = Highcharts.chart('MyChart', {
                chart: {
                    type: 'column',
                    options3d: {
                        enabled: true,
                        alpha: 15,
                        beta: 15,
                        viewDistance: 25,
                        depth: 40
                    }
                },

                title: {
                    text: '{{trans('app.labels.comparative_graph')}} ({{$statementDate}})'
                },

                xAxis: {
                    categories: ['{{trans('provision.labels.provisioned')}}', '{{trans('provision.labels.posted')}}']
                },

                yAxis: {
                    allowDecimals: false,
                    min: 0,
                    title: {
                        text: '{{trans('app.labels.values')}} ({{trans('app.labels.currency_symbol')}})'
                    }
                },

                tooltip: {
                    headerFormat: '<b>{point.key}</b><br>',
                    pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: {point.y} / {point.stackTotal}'
                },

                plotOptions: {
                    column: {
                        stacking: 'normal',
                        depth: 40
                    }
                },

                colors: ['#2AB27B', '#BF5329'],

                series: [{
                    name: '{{trans('app.labels.income')}}',
                    data: [{{$totalCreditProvision}}, {{$totalCredit}}],
                    stack: 'income'
                }, {
                    name: '{{trans('app.labels.expenses')}}',
                    data: [{{$totalDebitProvision}}, {{$totalDebit}}],
                    stack: 'expenses'
                }]
            });

            $('.highcharts-credits').hide();
        });
    </script>
@endsection
