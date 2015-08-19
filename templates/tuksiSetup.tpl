<html>
<body>
<h1>Tuksi setup - step {$step} of {$maxstep}</h1>

{foreach name=step item=step from=$steps}
Step {$step.no} - {$step.name}<br>
{/foreach}
</body>
</html>
