import React, { Component } from 'react';
import { Admin, Resource } from 'react-admin';
import authProvider from './authProvider';
import dataProvider from './dataProvider';
import apiDocumentationParser from "./apiDocumentationParser";
import { QuizList, QuizIcon, QuizEdit, QuizCreate } from './resource/quiz';
import { QuestionList, QuestionIcon, QuestionEdit, QuestionCreate } from './resource/question';
import { UserList, UserIcon } from './resource/user';

const entrypoint = process.env.REACT_APP_API_ENTRYPOINT;

export default class extends Component {
    state = { api: null };

    componentDidMount() {
        apiDocumentationParser(entrypoint).then(({ api }) => {
            this.setState({ api });
        }).catch((e) => {
            console.log(e);
        });
    }

    render() {
        if (null === this.state.api) return <div>Loading...</div>;
        return (
            <Admin api={ this.state.api }
               apiDocumentationParser={ apiDocumentationParser }
               dataProvider= { dataProvider(this.state.api) }
               authProvider={ authProvider }
            >
                <Resource name="questions" list={QuestionList} create={QuestionCreate} edit={QuestionEdit} icon={QuestionIcon}/>
                <Resource name="quizzes" list={QuizList} create={QuizCreate} edit={QuizEdit} icon={QuizIcon}/>
                <Resource name="users" list={UserList} icon={UserIcon}/>
                <Resource name="answers"/>
            </Admin>
        )
    }
}
