
<?php
function colorea($pos,$from='66cc00', $to='cc2200' ) 
{   
    $pos=$pos/100;

    // 1. Grab RGB from each colour 
    list($fr, $fg, $fb) = sscanf($from, '%2x%2x%2x'); 
    list($tr, $tg, $tb) = sscanf($to, '%2x%2x%2x'); 
     
    // 2. Calculate colour based on frational position 
    $r = (int) ($fr - (($fr - $tr) * $pos))+20; 
    $g = (int) ($fg - (($fg - $tg) * $pos))+20; 
    $b = (int) ($fb - (($fb - $tb) * $pos))+20; 
     
    // 3. Format to 6-char HEX colour string 
    return 'style="padding:0 7px;background-color: #'.sprintf('%02x%02x%02x', $r, $g, $b).'"'; 
}  

?>


<!-- Modal -->

        <div class="modal fade" id="infoModal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ trans('labels.backend.messages.title_') }}</h4>
              </div>
            <div class="modal-body">
             
              <div>
                <div>
                    <b>Hora</b>
                </div>
                <div>
                    <pre id="id_text_timedate">
                    </pre>
                </div>
              </div>


              <div>
                <div>
                    <b>Ultimo Error</b>
                </div>
                <div>
                    <pre id="id_text_errors">
                    </pre>
                </div>
              </div>

              <div>
                <div>
                    <b>Ultimo Acceso</b>
                </div>
                <div>
                    <pre id="id_text_access">
                    </pre>
                </div>
              </div>
          </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
<div class="box box-solid box-primary">
    <div class="box-tools pull-right">

    </div>
    <div class="box-body">
        <form action="" method="GET">
            <div class="col-xs-4">
                Date <input type="date" name="date" class="form-control" value="{{request()->get('date')}}" >
            </div>
            <div class="col-xs-2"><br>
                <button type="submit" class="btn btn-default">Cargar fecha</button>
            </div>
        </form>
    </div>
</div>
<div class="box box-solid box-primary">
    <div class="box-tools pull-right">

    </div>
    <div class="box-body">
        <table class="table table-bordered" id="messages-table">
            <tr>
                <th>Usage Cpu</th>
                <th>Load av1</th>
                <th>Load av2</th>
                <th>Load av3</th>
                <th>Load Memory</th>
                <th>Load Swap</th>
                <th>Used Disk</th>
                <th>Memory Php</th>
                <th>Process Php</th>
                <th>FPM idle proc</th>
                <th>FPM active proc</th>
                <th>FPM slow pages</th>
                <th>FPM queue </th>
                <th>Created</th>
            </tr>   

            @foreach ($status_ as $statu)

            <tr style="cursor: pointer" onclick="document.getElementById('id_text_timedate').innerHTML='{{$statu->created_at}}';document.getElementById('id_text_errors').innerHTML='{{$statu->last_error}}.'.replace('\\n', '\n');document.getElementById('id_text_access').innerHTML='{{$statu->last_access}}.'.replace('\\n', '\n');$('#infoModal').modal('show');">
                <td {!! colorea($statu->cpu)!!}><b>{{$statu->cpu}} %</b></td>
                <td {!! colorea(($statu->lv1>6?100:($statu->lv1*20)))!!}>{{$statu->lv1}}</td>
                <td {!! colorea(($statu->lv2>6?100:($statu->lv2*20)))!!}>{{$statu->lv2}}</td>
                <td {!! colorea(($statu->lv3>6?100:($statu->lv3*20)))!!}>{{$statu->lv3}}</td>
                <td {!! colorea($statu->memory)!!}>{{$statu->memory}} %</td>
                <td {!! colorea($statu->memorySwap)!!}>{{$statu->memorySwap}} %</td>
                <td {!! colorea($statu->disk)!!}>{{$statu->disk}} %</td>
                <td {!! colorea($statu->memoryPhp/100)!!}>{{$statu->memoryPhp}}MB</td>
                <td {!! colorea($statu->processPhp)!!}>{{$statu->processPhp}}</td>
                <td {!! colorea($statu->fpm_idle_processes)!!}>{{$statu->fpm_idle_processes}}</td>
                <td {!! colorea($statu->fpm_active_processes)!!}>{{$statu->fpm_active_processes}}</td>
                <td title="pages max 10s per 5 min" {!! colorea(1.5*($statu->fpm_slow_requests-$last_slow<0?0:($statu->fpm_slow_requests-$last_slow)))!!}>{{($statu->fpm_slow_requests-$last_slow<0?0:($statu->fpm_slow_requests-$last_slow))}}</td>
                <td {!! colorea($statu->fpm_listen_queue)!!}>{{$statu->fpm_listen_queue}}</td>
                <td style="padding:0 7px">{{$statu->created_at}}</td>
                <td>
                </td>
            </tr>    
            @php
                $last_slow=$statu->fpm_slow_requests;
            @endphp
            @endforeach
            <tr>
                <th>Usage Cpu</th>
                <th>Load av1</th>
                <th>Load av2</th>
                <th>Load av3</th>
                <th>Load Memory</th>
                <th>Load Swap</th>
                <th>Used Disk</th>
                <th>Memory Php</th>
                <th>Process Php</th>
                <th>FPM idle proc</th>
                <th>FPM active proc</th>
                <th>FPM slow pages</th>
                <th>FPM queue </th>
                <th>Created</th>
            </tr>   
        </table>
    </div>
</div>
