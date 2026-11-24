# How to plan/do a MAJOR release

The following steps must be done,
when a MAJOR release `n+1.0.0` is planned or happens:

1. Update the branch alias in [`composer.json`](../../../composer.json)
   at `extra.branch-alias.dev-main`
   to `n+1.x-dev`
   on `main` branch.
1. Create a branch `n.x` from `main`.
1. Optional: Remove the `extra.branch-alias`
   in [`composer.json`](../../../composer.json)
   on `n.x` branch.
1. Add branch `n.x` to "branch protection"
   and copy the settings from currently protected `main` to it.
1. Do the usual dev-/release-process in `main` branch.
