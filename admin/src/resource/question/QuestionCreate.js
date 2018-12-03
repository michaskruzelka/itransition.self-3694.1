import React from 'react';
import {
    required,
    Create,
    SimpleForm,
    LongTextInput,
    BooleanInput,
    ArrayInput,
    SimpleFormIterator,
} from 'react-admin';

const validateAnswers = (answers) => {
    const $message = 'There must be exactly 1 correct answer';
    if (answers) {
        const correctOnes = answers.filter(answer => answer.isCorrect);
        return 1 === correctOnes.length ? undefined : $message;
    }
    return $message;
};

export default props => (
    <Create {...props}>
        <SimpleForm redirect="list">
            <LongTextInput source="content" validate={required()}/>
            <ArrayInput source="answers" validate={validateAnswers}>
                <SimpleFormIterator>
                    <LongTextInput source="content"/>
                    <BooleanInput source="isCorrect" label="Is Correct"/>
                </SimpleFormIterator>
            </ArrayInput>
        </SimpleForm>
    </Create>
);
