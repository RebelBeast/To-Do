<template>
  <div>
      <input type="text" class="todo-input" placeholder="Was muss erledigt werden" v-model="newTodo" @keyup.enter="addTodo">
      <todo-item v-for="(todo, index) in todosFiltered" :key="todo.id" :todo="todo" :index="index" :checkAll="!anyRemaining">
      </todo-item>
      <div class="extra-container">
          <div><label><input type="checkbox" :checked="!anyRemaining" @change="checkAlltodos"> Alle Abschließen </label></div>
          <div>{{ remaining }} Aufgaben offen</div>
      </div>
      <div class="extra-container">
          <div>
              <button :class="{ active: filter == 'all'}" @click="filter = 'all'">Alle</button>
              <button :class="{ active: filter == 'active'}" @click="filter = 'active'">Offen</button>
              <button :class="{ active: filter == 'completed'}" @click="filter = 'completed'">Abgeschlossen</button>
          </div>
          <div>
              <button v-if="showClearCompletedButton" @click="clearCompleted">Abgeschlossene löschen</button>
          </div>
      </div>
  </div>
</template>

<script>
import TodoItem from './TodoItem'

export default {
  name: 'todo-list',
  components: {
  TodoItem,
},
  data () {
    return {
      newTodo: '',
      idForTodo: 3,
      beforeEditCache: '',
      filter: 'all',
      todos: [
          {
              'id': 1,
              'title': 'Finish Vue Screen Cast',
              'completed': false,
              'editing': false,
          },
          {
              'id': 2,
              'title': 'Take over the World',
              'completed': false,
              'editing': false,
          },
      ]
    }
  },
  async mounted () {
    await this.fetchTodos()
  },
  created() {
    eventBus.$on('removedTodo', (index) => this.removeTodo(index))
    eventBus.$on('finishedEdit', (data) => this.finishedEdit(data))
  },
  computed: {
      remaining() {
          return this.todos.filter(todo => !todo.completed).length
      },
      anyRemaining() {
          return this.remaining  !== 0
      },
      todosFiltered() {
          if(this.filter == 'all') {
              return	this.todos
          } else if (this.filter == 'active') {
              return this.todos.filter(todo => !todo.completed)
          } else if (this.filter == 'completed') {
              return this.todos.filter(todo => todo.completed)
          }
          return this.todos
      },
      showClearCompletedButton() {
          return this.todos.filter(todo => todo.completed).length > 0
      }
  },
  methods: {
      async addTodo() {
          if (this.newTodo.trim() === '') {
              return
          }
          const temptodo = {
            id: this.idForTodo,
              title: this.newTodo,
              completed: false,
          }
          const response =await fetch("http://localhost:80/todos", {
            method:"POST",
            headers:{
              "Content-Type":"application/json"
            }, 
            body: JSON.stringify(temptodo)
            })
            if (response.ok) {
              this.todos.push(temptodo)}

          this.newTodo = ''
          this.idForTodo++
      },
      async removeTodo(index) {
          const response = await fetch("http://localhost:80/todos", {
            method:"DELETE",
            headers:{
              "Content-Type":"application/json"
            }, 
            body:this.todos[index].id
          })
          if (response.ok) {
            this.todos.splice(index, 1)
          }
      },
      checkAlltodos() {
          this.todos.forEach((todo) => todo.completed = event.target.checked)
      },
      clearCompleted() {
          this.todos = this.todos.filter(todo => !todo.completed)
      },
      async finishedEdit(data) {
        console.log(data.todo)
        const response = await fetch("http://localhost:80/todos",{
          method:"PUT",
          headers:{
            "Content-Type":"application/json"
          },
          body:JSON.stringify(data.todo)
        })
        if(response.ok) {
        this.todos.splice(data.index, 1, data.todo)}
      },
      async fetchTodos() {
  const response = await fetch("http://localhost:80/todos");
  console.log(response);
  if (response.ok) {
    response.json().then(todojson => {
      this.todos = todojson; 
    });
  }
}


      }
  }
</script>



  
  <style >
  .todo-input {
    width: 100%;
    padding: 10px 18px;
    font-size: 18px;
    margin-bottom: 16px;
  }

  .todo-input:focus {
    outline: 0;
  }

  .todo-item {
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .remove-item {
    cursor: pointer;
    margin-left: 14px;
  }

  .remove-item:hover {
    color: black;
  }

  .todo-item-left {
    display: flex;
    align-items: center;
  }

  .todo-item-label {
    padding: 10px;
    border: 1px solid white;
    margin-left: 12px;
  }

  .todo-item-edit {
    font-size: 24px;
    color: #2c3e50;
    margin-left: 12px;
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    font-family: 'Avenir', Arial, Helvetica, sans-serif;
  }

  .todo-item-edit:focus {
    outline: none;
  }

  .completed {
    text-decoration: line-through;
    color: grey;
  }

  .extra-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 16px;
    border-top: 1px solid lightgrey;
    padding-top: 14px;
    margin-bottom: 14px;
  }

  button {
    font-size: 14px;
    background-color: white;
    appearance: none;
  }

  button:hover {
    background: lightgreen;
  }

  button:focus {
    outline: none;
  }

  .active {
    background: lightgreen;
  }

  </style>
  