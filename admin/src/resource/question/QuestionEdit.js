import React from 'react';
import {
    required,
    Edit,
    SimpleForm,
    LongTextInput,
    BooleanInput,
    ArrayInput,
    SimpleFormIterator
} from 'react-admin';

const QuestionTitle = ({ record }) => {
    return <span>Question {record ? `"${record.originId}"` : ''}</span>;
};

const validateAnswers = (answers) => {
    const $message = 'There must be exactly 1 correct answer';
    if (answers) {
        const correctOnes = answers.filter(answer => answer.isCorrect);
        return 1 === correctOnes.length ? undefined : $message;
    }
    return $message;
};

export default props => (
    <Edit title={<QuestionTitle/>} {...props}>
        <SimpleForm>
            <LongTextInput source="content" validate={required()}/>
            <ArrayInput source="answers" resource="answers" validate={validateAnswers} key="id">
                <SimpleFormIterator key="id">
                    <LongTextInput source="content" key="id"/>
                    <BooleanInput source="isCorrect" label="Is Correct"/>
                </SimpleFormIterator>
            </ArrayInput>
        </SimpleForm>
    </Edit>
);
