import MeQuery from '../../services/queries/auth/me.query.gql'

export default {
  async me({ commit }) {
    const { data } = await this.app.apolloProvider.defaultClient.query({
      query: MeQuery,
    })
    return data
  },
}
