export default {
  state: {
    conversation: [],
    hubUrl: null,
  },
  getters: {
    CONVERSATIONS: (state) =>
      state.conversation.sort((a, b) => {
        return a.createdAt < b.createdAt;
      }),
    MESSAGES: (state) => (conversationID) => {
      return state.conversation.find(
        (i) => i.conversationID === parseInt(conversationID)
      ).messages;
    },
    HUBURL: (state) => state.hubUrl,
  },
  mutations: {
    SET_CONVERSATIONS: (state, payload) => {
      state.conversation = payload;
    },
    SET_MESSAGES: (state, { conversationID, payload }) => {
      state.conversation.find(
        (i) => i.conversationID === parseInt(conversationID)
      )["messages"] = payload;
    },
    ADD_MESSAGE: (state, { conversationID, payload }) => {
      if (conversationID) {
        state.conversation
          .find((i) => i.conversationID === parseInt(conversationID))
          ["messages"].push(payload);
      }
    },
    SET_CONVERSATION_LAST_MESSAGE: (state, { conversationID, payload }) => {
      let rs = state.conversation.find(
        (i) => i.conversationID === parseInt(conversationID)
      );

      rs.content = payload.content;
      rs.createdAt = payload.createdAt;
    },
    SET_HUBURL: (state, payload) => (state.hubUrl = payload),
    UPDATE_CONVERSATIONS: (state, payload) => {
      let rs = state.conversation.find(
        (i) => i.conversationID === payload.conversation.id
      );

      rs.content = payload.content;
      rs.createdAt = payload.createdAt;
    },
  },
  actions: {
    GET_CONVERSATIONS: ({ commit }) => {
      return fetch(`/conversations`)
        .then((result) => {
          const huburl = result.headers
            .get("Link")
            .match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1];
          commit("SET_HUBURL", huburl);

          return result.json();
        })
        .then((result) => {
          commit("SET_CONVERSATIONS", result);
        });
    },
    GET_MESSAGES: ({ commit, getters }, conversationID) => {
      if (getters.MESSAGES(conversationID) === undefined) {
        return fetch(`/messages/${conversationID}`)
          .then((result) => result.json())
          .then((result) => {
            commit("SET_MESSAGES", { conversationID, payload: result });
          });
      }
    },
    POST_MESSAGE: ({ commit }, { conversationID, content }) => {
      let formData = new FormData();
      formData.append("content", content);

      return fetch(`/messages/create/${conversationID}`, {
        method: "POST",
        body: formData,
      })
        .then((result) => result.json())
        .then((result) => {
          commit("ADD_MESSAGE", { conversationID, payload: result });
          commit("SET_CONVERSATION_LAST_MESSAGE", {
            conversationID,
            payload: result,
          });
        });
    },
  },
};
