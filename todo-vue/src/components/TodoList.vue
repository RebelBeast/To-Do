<template>
    <div>
        <input type="text" class="todo-input" placeholder="Was muss erledigt werden" v-model="newTodo" @keyup.enter="addTodo">
        <div v-for="todo in todos" :key="todo.id" class="todo-item">
            {{ todo.title }}
        </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'todo-list',
    data () {
      return {
        newTodo: '',
        idForTodo: 3,
        todos: [
            {
                'id': 1,
                'title': 'Finish Vue Screen Cast',
                'completed': false,
            },
            {
                'id': 1,
                'title': 'Take over the World',
                'completed': false,
            },
        ]
      }
    },
    methods: {
        addTodo() {
            this.todos.push({
                id: this.idForTodo,
                title: this.newTodo,
                completed: false,
            })

            this.newTodo = ''
            this.idForTodo++
        }

        }
    }
  </script>
  
  <style >
  .todo-input {
    widows: 100%;
    padding: 10px 18px;
    font-size: 18px;
    margin-bottom: 16px;
  }

  </style>
  