<center>

<h1>Ajedrez</h1>

<p>Juegan las {$turnStr}. Encuentre la mejor jugada.</p>

{$board}

<p>Dificultad: {$level}</p>

<p>Seleccione aqu&iacute; para ver la soluci&oacute;n: [<font color="white">{$solution}</font>]</p>

{space10}

<p>Otras dificultades:</p>
{if $level ne "Fácil"}{button href="AJEDREZ FACIL" caption="F&aacute;cil"}{/if}
{if $level ne "Intermedio"}{button href="AJEDREZ" caption="Mediano"}{/if}
{if $level ne "Difícil"}{button href="AJEDREZ DIFICIL" caption="D&iacute;ficil"}{/if}

</center>
