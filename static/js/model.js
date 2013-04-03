var Survey = Backbone.Model.extend({
	defaults : function() {return {
		"id" : -1,
		"title" : "请在这里修改问卷的标题",
		"description" : "请在这里给你的问卷一个简单的表述",
		"questions" : []
	}}
});

var QuestionList = Backbone.Collection.extend({
	model: Question
});

var Result = Backbone.Model.extend({});

var Question = Backbone.Model.extend({
	defaults: {
		'edit_mode': false
	}
});
window.question_sample = {
	'multi-choice': {
		id: -1,
		type: 'multi-choice',
		content: '请你修改这里的内容',
		choices: [{'content': '第一个选项'}, {'content': '第二个选项'}, {'content': '第三个选项'}, {'content': '第四个选项'}, {'content': '其他', 'other': true}],
		other: true,
		column: 1
	},
	'single-choice': {
		id: -1,
		type: 'single-choice',
		content: '请你修改这里的内容',
		choices: [{'content': '第一个选项'}, {'content': '第二个选项'}, {'content': '第三个选项'}, {'content': '第四个选项'}, {'content': '其他', 'other': true}],
		other: true,
		column: 1
	},
	'text': {
		id: -1,
		type: 'text',
		content: '请你修改这里的内容'
	},
	'single-dropdown': {
		id: -1,
		type: 'single-dropdown',
		content: '请你修改这里的内容',
		choices: ['第一个选项', '第二个选项', '第三个选项', '第四个选项']
	},
	'multi-dropdown': {
		id: -1,
		type: 'multi-dropdown',
		content: '请你修改这里的内容',
		choices: ['第一个选项', '第二个选项', '第三个选项', '第四个选项']
	},
	'single-matrix': {
		id: -1,
		type: 'single-matrix',
		content: '请你修改这里的内容',
		vals: ['选项', '选项', '选项', '选项', '选项'],
		questions: [['请修改这里的问题', 1], ['请修改这里的问题', 2], ['请修改这里的问题', 3]]
	},
	'multi-matrix': {
		id: -1,
		type: 'multi-matrix',
		content: '请你修改这里的内容',
		vals: ['选项', '选项', '选项', '选项', '选项'],
		questions: [['请修改这里的问题', 1], ['请修改这里的问题', 2], ['请修改这里的问题', 3]]
	}
};
