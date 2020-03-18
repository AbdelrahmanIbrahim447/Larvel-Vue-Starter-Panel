export default class Gate{

	constructor(user){
		this.user = user;
	}

	isAdmin(){
		return this.user === 'admin';
	}
	isAuthor(){
		return this.user === 'author';
	}
	isUser(){
		return this.user === 'user';
	}
}