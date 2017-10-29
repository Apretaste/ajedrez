<center>

<h1>Problemas de Ajedrez</h1>

<p>Juegan las {$turnStr}. Encuentre la mejor jugada.</p>

{$board}

<p>Dificultad: {$level}</p>

<p>Seleccione abajo para ver la soluci&oacute;n: <br/>[<font color="white">{$solution}</font>]</p>

{space10}

<p>Otras dificultades:</p>
{if $level ne "Fácil"}{button href="AJEDREZ FACIL" caption="F&aacute;cil" size="small"}{/if}
{if $level ne "Intermedio"}{button href="AJEDREZ" caption="Mediano" size="small"}{/if}
{if $level ne "Difícil"}{button href="AJEDREZ DIFICIL" caption="D&iacute;ficil" size="small"}{/if}

</center>
