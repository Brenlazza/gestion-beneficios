<?php
require __DIR__.'/../header.php'; require __DIR__.'/../navbar.php';
$maxVenta=1; foreach($ventas as $v) $maxVenta=max($maxVenta,$v['total']);
$maxSemana=1; foreach($semanal as $s) $maxSemana=max($maxSemana,$s['total']);
?>
<main class="container-fluid py-4 px-lg-4">
 <div class="mb-4"><h2 class="mb-0">Reportes</h2><span class="text-secondary">Resumen de ventas, recaudación, rendiciones y stock.</span></div>
 <div class="row g-3 mb-4">
  <div class="col-xl-6"><div class="card p-4 h-100"><h5>Ventas por vendedor</h5>
   <?php if(!$ventas):?><div class="vacio">No hay datos.</div><?php else:foreach($ventas as $v):?>
   <div class="mb-3"><div class="d-flex justify-content-between"><span><?=e($v['vendedor'])?></span><strong>$<?=number_format($v['total'],2,',','.')?></strong></div><div class="barra-grafico"><span style="width:<?=round($v['total']/$maxVenta*100)?>%"></span></div><small class="text-secondary"><?=e($v['cantidad'])?> ventas</small></div>
   <?php endforeach;endif;?>
  </div></div>
  <div class="col-xl-6"><div class="card p-4 h-100"><h5>Ventas de los últimos 7 días</h5>
   <?php if(!$semanal):?><div class="vacio">No hay ventas esta semana.</div><?php else:foreach($semanal as $s):?>
   <div class="mb-3"><div class="d-flex justify-content-between"><span><?=e($s['dia'])?></span><strong>$<?=number_format($s['total'],2,',','.')?></strong></div><div class="barra-grafico"><span class="bg-success" style="width:<?=round($s['total']/$maxSemana*100)?>%"></span></div></div>
   <?php endforeach;endif;?>
  </div></div>
 </div>
 <div class="row g-3 mb-4">
  <div class="col-xl-6"><div class="card"><div class="card-header bg-white"><h5 class="mb-0">Resultado por campaña</h5></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Campaña</th><th>Vendido</th><th>Recaudado</th><th>Pendiente</th></tr></thead><tbody><?php if(!$campanias):?><tr><td colspan="4" class="vacio">Sin campañas.</td></tr><?php else:foreach($campanias as $c):?><tr><td><?=e($c['nombre'])?></td><td>$<?=number_format($c['vendido'],2,',','.')?></td><td>$<?=number_format($c['recaudado'],2,',','.')?></td><td>$<?=number_format(max(0,$c['vendido']-$c['recaudado']),2,',','.')?></td></tr><?php endforeach;endif;?></tbody></table></div></div></div>
  <div class="col-xl-6"><div class="card"><div class="card-header bg-white"><h5 class="mb-0">Stock por campaña</h5></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Producto</th><th>Campaña</th><th>Stock</th></tr></thead><tbody><?php if(!$stock):?><tr><td colspan="3" class="vacio">Sin productos.</td></tr><?php else:foreach($stock as $p):?><tr><td><?=e($p['nombre'])?></td><td><?=e($p['campania'])?></td><td><span class="badge text-bg-<?=$p['stock_actual']<=$p['stock_minimo']?'warning':'success'?>"><?=e($p['stock_actual'])?></span></td></tr><?php endforeach;endif;?></tbody></table></div></div></div>
 </div>
 <div class="card p-4"><h5>Resumen de rendiciones</h5><div class="row g-3 mt-1"><?php if(!$rendiciones):?><div class="vacio">No hay rendiciones registradas.</div><?php else:foreach($rendiciones as $r):?><div class="col-md-4"><div class="border rounded p-3"><small class="text-secondary"><?=e($r['estado'])?></small><h4 class="mb-0">$<?=number_format($r['total'],2,',','.')?></h4><span><?=e($r['cantidad'])?> rendiciones</span></div></div><?php endforeach;endif;?></div></div>
</main><?php require __DIR__.'/../footer.php';?>
