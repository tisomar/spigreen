/**
 * Script respons?vel pelas cofigura??es do sistema
 * 
 * @author  H?di Carlos Minin - hedicarlos@gmail.com
 */

var _config = {
	windowTitle : 'Atendimento Online',
	initialMessage : 'Olá Sr(a) {NAME}, agradecemos pelo seu contato. Em que posso lhe ajudar? Para agilizar o atendimento por favor informe seu número de telefone (com DDD). Em que posso ajudá-lo?'
};

var _themes = [
	{theme: 'basic', name : 'Basic'},
	{theme: 'nature', name : 'Nature'}
];

var _options = {
	enableSound : true
};

var _predefined_messages = [
	{shortcut : '/aguarde', message : 'Aguarde um momento por favor'}
]

var _info = {
	name : 'brTalk',
	version : '1.0.2',
	author : 'Hédi Carlos Minin',
	email : 'hedicarlos@gmail.com'
}
